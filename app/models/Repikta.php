<?php 
	/**
	* 
	*/
	class Repikta
	{
		
		function __construct()
		{
			
		}

		public function Generate_Transkrip_Kriteria($idgroup){
			$id_result = DB::table('kmeans_result')->where('id_group', '=' , $idgroup)->max('id');
			$data_kluster = KmeansResult::find($id_result);
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
						$dokumen = Dokumen::find($hasil_kluster[$i][$j]);
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
			$data = dbTranskripKriteria::where('group','=',$idgroup)->get();
			$mhs = Dokumen::find($nrp);
			$mhs_transkrip = json_decode($mhs->transkrip);
			$arrTranskrip = json_decode($data[0]->kriteria_transkrip);
			
			$distance = array();
			for ($i=0; $i < count($arrTranskrip); $i++) { 
				$d = $this->EuclideanTranskrip($arrTranskrip[$i], $mhs_transkrip);
				$distance[$i] = $d;
				echo "[".$i."] ".$d."<br />";
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
	}
?>