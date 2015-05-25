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

		public function GetClosest($nrp, $id_kluster){
			$n = 5;

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

			$kluster = $this->Choose_Kluster($nrp,$idgroup_result);
			$mahasiswa = dbDokumen::find($nrp);
			echo "NRP : ".$mahasiswa->nrp."<br />";
			echo "NAMA : ".$mahasiswa->nama."<br /><br />";
			echo "Anda direkomendasikan memilih Tugas Akhir pada bidang : ";
			switch ($kluster) {
				case 0:
					echo "<b>Rekayasa Perangkat Lunak</b><br />";
					break;
				case 1:
					echo "<b>Kecerdasan Citra dan Visual</b><br />";
					break;
				case 2:
					echo "<b>Komputasi Berbasis Jaringan</b><br />";
					break;
			}
			echo "<br />";
			$nrp_terdekat_kluster = $this->GetClosest($nrp, $kluster);
			$lda_result = dbLdaSave::where('percobaan_ke','=',$id_hasil_lda)
								->where('kluster_ke','=',$kluster)->get();
			$list_nrp = json_decode($lda_result[0]->daftar_dokumen);
			$theta_matrix = json_decode($lda_result[0]->matriks_theta);
			$phi_matrix = json_decode($lda_result[0]->matriks_phi);
			$k = $lda_result[0]->k_topik;
			$nterm = $lda_result[0]->n_term;
			$list_term = json_decode($lda_result[0]->matriks_term);

			$topic = $this->Get20TermTopic($k, $phi_matrix, $nterm, $list_term);
			
			echo "Berikut Daftar Kata-Kata Topik yang direkomendasikan menurut prosentase kedekatan : <br /><br >";

			// echo "--- ".$index_nrp."<br />";
			// var_dump($list_nrp);
			$topik_terdekat = array();
			$kemunculan_topik = array();
			
			for ($i=0; $i <$k ; $i++) { 
				$kemunculan_topik[$i]=0;
			}
			foreach ($nrp_terdekat_kluster as $key => $value) {
				$index_nrp = array_search($value, $list_nrp);
				$vtopic = $theta_matrix[$index_nrp];
				$idtopic = array_search(max($vtopic), $vtopic);
				array_push($topik_terdekat, $idtopic);
				$kemunculan_topik[$idtopic]++;
				//echo $value."-".$idtopic."<br />";
			}
			//var_dump($kemunculan_topik);
			$distinct = array_unique($kemunculan_topik);
			arsort($distinct);
			//var_dump($distinct);

			$urutan_idtopic = array();
			echo "<html>";
			echo "<table>";
			echo "<tr>";
			foreach ($distinct as $key => $value) {
				if($value!=0){
					
					for ($i=0; $i <$k ; $i++) { 
						if($kemunculan_topik[$i]==$value){
							echo "<th>"."Topik ".($i+1)." [".(float)($value/$k)."%]"."</th>";
							array_push($urutan_idtopic, $i);
						}
					}
					
				}
			}
			echo "</tr>";
			// var_dump($urutan_idtopic);
			// echo count($topic)."<br />";

			for ($x=0; $x <20 ; $x++) { 
				echo "<tr>";
				for ($i=0; $i <count($urutan_idtopic) ; $i++) { 
					echo "<td>".$topic[$urutan_idtopic[$i]][$x]."</td>";
				}
				echo "</tr>";
			}


			//   
			//     <th>Month</th>
			//     <th>Savings</th>
			//   
			//   <tr>
			//     <td>January</td>
			//     <td>$100</td>
			//   </tr>
			echo "</table>";
			echo "</html>";
		}
	}
?>