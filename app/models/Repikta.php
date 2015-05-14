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

		public function GetClosest(){
			$dokumen_testing = Dokumen::where('training','=',false)->get();
			$dt_kluster = KmeansResult::find(5);
			$hasil_kluster = json_decode($dt_kluster->hasil_kluster);
			foreach ($dokumen_testing as $key => $value) {
				//echo $value->nrp."<br />";
				//$data = dbTranskripDistanceAll::where('nrp','=',$value->nrp)->orderBy('distance', 'asc')->take(5)->get();
				$data = dbTranskripDistanceKluster::where('nrp','=',$value->nrp)->orderBy('distance', 'asc')->take(5)->get();
				//echo $data[0]->distance."<br />";
				$pem = $data[4]->pembanding;

				// foreach ($data as $key => $dt) {
				// 	echo $dt->nrp." - ".$dt->pembanding." - ".$dt->distance." - ";
					
					for ($i=0; $i <count($hasil_kluster) ; $i++) { 
						//if(in_array($dt->pembanding, $hasil_kluster[$i])==true){
						if(in_array($pem, $hasil_kluster[$i])==true){
							switch ($i) {
								case 0:
									echo "RPL<br />";
									break;
								case 1:
									echo "KCV<br />";
									break;
								case 2:
									echo "KBJ<br />";
									break;
							}
						}
					}
				// }
			}
		}
	}
?>