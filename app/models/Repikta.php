<?php 
	/**
	* 
	*/
	class Repikta
	{

		public function Generate_Transkrip_Kriteria($idgroup){
			$id_result = DB::table('kmeans_result')->where('id_group', '=' , $idgroup)->max('id');
			$data_kluster = dbKmeansResult::find($id_result);
			$hasil_kluster = json_decode($data_kluster->hasil_kluster);
			$k = $data_kluster->jumlah_kluster;

			$matakuliah = dbMataKuliah::all();

			$arrTranskrip = array();
			for ($i=0; $i <$k ; $i++) { 
				$n = count($hasil_kluster[$i]);
				$arrTranskrip[$i] = (object) array();
				foreach ($matakuliah as $key => $mk) {
					$kode = $mk->mk_kode;
					$sum = 0.0;
					for ($j=0; $j <$n ; $j++) { 
						$dokumen = dbDokumen::find($hasil_kluster[$i][$j]);
						$transkrip = json_decode($dokumen->transkrip);
						$sum += $transkrip->$kode;
					}
					$avg = $sum/$n;
					$arrTranskrip[$i]->$kode = $avg;
				}
			}
			$simpan = new dbTranskripKriteria;
			$simpan->group = $idgroup;
			$simpan->id_kluster = $id_result;
			$simpan->kriteria_transkrip = json_encode($arrTranskrip);
			$simpan->save();
		}

		public function Choose_Kluster($nrp, $idgroup){
			//
			$data = dbTranskripKriteria::where('group','=',$idgroup)->get();
			$mhs = dbDokumen::find($nrp);
			$mhs_transkrip = json_decode($mhs->transkrip);
			$arrTranskrip = json_decode($data[0]->kriteria_transkrip);
			
			$distance = array();
			for ($i=0; $i < count($arrTranskrip); $i++) { 
				$d = $this->EuclideanTranskrip($arrTranskrip[$i], $mhs_transkrip);
				$distance[$i] = $d;
				//echo "[".$i."] ".$d."<br />";
			}

			$index = array_search(min($distance), $distance);
			return $index;
		}

		public function EuclideanTranskrip($nrp, $pembanding){
			$matakuliah = dbMataKuliah::all();
			$fill = 0.0;
			foreach ($matakuliah as $key => $mk) {
				$kode = $mk->mk_kode;
				$fill += pow(($nrp->$kode - $pembanding->$kode), 2);
			}
			$d = sqrt($fill);
			return $d;
		}

		public function GetClosest($nrp, $id_kluster, $n){
			$data = dbTranskripDistanceKluster::where('nrp','=',$nrp)
											->where('index','=',$id_kluster)
											->orderBy('distance', 'asc')
											->take($n)->get();

			$nrp_closest = array();
			foreach ($data as $key => $value) {
				array_push($nrp_closest, $value->pembanding);
			}

			return $nrp_closest;
		}

		public function Get20TermTopic($ktopik, $phi, $nterm, $list_term)
		{
			$arr = array();
			for ($k=0; $k <$ktopik ; $k++) { 
				$hasil = array();
				for ($n=0; $n <$nterm ; $n++) { 
					$hasil[$list_term[$n]] = $phi[$n][$k];	
				}
				arsort($hasil);
				$top20 = array_slice($hasil,0,20);
				$terms = array();
				foreach ($top20 as $key => $value) {
					//echo $key."<br />"; $i++;
					array_push($terms, $key);
				}
				$arr[$k] = $terms;
			}
			return $arr;
		}

		public function RekomendasiTopik($nrp)
		{
			$idgroup_result = 2;
			$id_hasil_lda = 11;
			$n = 5;

			//MENCARI KLUSTER PILIHAN
			$kluster = $this->Choose_Kluster($nrp,$idgroup_result);

			//GET DATA MAHASISWA
			$mahasiswa = dbDokumen::find($nrp);
			// echo "NRP : ".$mahasiswa->nrp."<br />";
			// echo "NAMA : ".$mahasiswa->nama."<br /><br />";
			// echo "Anda direkomendasikan memilih Tugas Akhir pada bidang : ";
			// switch ($kluster) {
			// 	case 0:
			// 		echo "<b>Rekayasa Perangkat Lunak</b><br />";
			// 		break;
			// 	case 1:
			// 		echo "<b>Kecerdasan Citra dan Visual</b><br />";
			// 		break;
			// 	case 2:
			// 		echo "<b>Komputasi Berbasis Jaringan</b><br />";
			// 		break;
			// }
			// echo "<br />";
			
			//MENCARI n DOKUMEN TERDEKAT PADA KLUSTER TERPILIH
			$nrp_terdekat_kluster = $this->GetClosest($nrp, $kluster,$n);

			//MENDAPATKAN LDA TOPIK PADA KLUSTER TERPILIH
			$lda_result = dbLdaSave::where('percobaan_ke','=',$id_hasil_lda)
								->where('kluster_ke','=',$kluster)->get();

			$list_nrp = json_decode($lda_result[0]->daftar_dokumen);		//DAFTAR DOKUMEN PADA KLUSTER TERPILIH
			$theta_matrix = json_decode($lda_result[0]->matriks_theta);		//THETA LDA PADA KLUSTER TERPILIH
			$phi_matrix = json_decode($lda_result[0]->matriks_phi);			//PHI LDA PADA KLUSTER TERPILIH
			$k = $lda_result[0]->k_topik;									//JUMLAH TOPIK PADA KLUSTER TERPILIH
			$nterm = $lda_result[0]->n_term;								//BANYAK TERM LDA PADA KLUSTER TERPILIH
			$list_term = json_decode($lda_result[0]->matriks_term);			//DAFTAR TERM LDA PADA KLUSTER TERPILIH

			$topic = $this->Get20TermTopic($k, $phi_matrix, $nterm, $list_term); //LIST TERM TIAP TOPIK YANG AKAN DITAMPILKAN
			
			//echo "Berikut Daftar Kata-Kata Topik yang direkomendasikan menurut prosentase kedekatan : <br /><br >";

			$topik_terdekat = array();		//DAFTAR TOPIK n DOKUMEN TERDEKAT URUT BERDASARKAN DOKUMEN TERDEKAT
			$kemunculan_topik = array();	//JUMLAH KEMUNCULAN MASING-MASING TOPIK DALAM n DOKUMEN TERDEKAT
			
			for ($i=0; $i <$k ; $i++) { 
				$kemunculan_topik[$i]=0;
			}
			foreach ($nrp_terdekat_kluster as $key => $value) {
				$index_nrp = array_search($value, $list_nrp);		//mendapatkan id dokumen terdepat dalam matriks
				$vtopic = $theta_matrix[$index_nrp];				//daftar probabilitas topik dokumen
				$idtopic = array_search(max($vtopic), $vtopic);		//mendapatkan topik terpilih dokumen
				array_push($topik_terdekat, $idtopic);
				$kemunculan_topik[$idtopic]++;
			}

			//MENGURUTKAN TOPIK BERDASAR KEMUNCULAN TERBANYAK DARI n DOKUMEN
			arsort($kemunculan_topik);	

			// echo "<html>";
			// echo "<table>";

			// echo "<tr>";
			//ID TOPIK TERBANYAK UNTUK DIJADIKAN KRITERIA TOPIK
			$first=0; $max=-100; $topik_kriteria=array();
			foreach ($kemunculan_topik as $key => $value) {
				if($first==0) $max=$value;
				if($value==$max) array_push($topik_kriteria, $key);
				$first++;
				//if($value!=0) echo "<th>"."Topik ".($key+1)." [".(float)($value/$n)."%]"."</th>";
			}
			//echo "</tr>";

			for ($x=0; $x <20 ; $x++) { 
				//echo "<tr>";
				foreach ($kemunculan_topik as $key => $value) {
					//if($value!=0) echo "<td>".$topic[$key][$x]."</td>";
				}
				//echo "</tr>";
			}

			// echo "</table>";
			// echo "</html>";
			

			$simpan = new dbTestingRekomendasi;
			$simpan->nrp_testing = $nrp;
			$simpan->id_kmeans = $idgroup_result;
			$simpan->id_lda = $id_hasil_lda;
			$simpan->kluster_bidang = $kluster;
			$simpan->n_doc_terdekat = $n;
			$simpan->daftar_doc_terdekat = json_encode($nrp_terdekat_kluster);
			$simpan->topik_doc_terdekat = json_encode($topik_terdekat);
			$simpan->kemunculan_topik = json_encode($kemunculan_topik);
			$simpan->k_topik_terpilih = count($topik_kriteria);
			$simpan->daftar_topik_terpilih = json_encode($topik_kriteria);
			$simpan->save();

			return array($kluster, $topik_kriteria);
		}
	}
?>