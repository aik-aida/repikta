<?php
	/**
	* 
	*/
	class ClusterVariance
	{
		public $jumlahKluster;
		public $dataKluster;
		public $rata2kluster;
		public $idSimpan;
		public $idTeks;
		
		function __construct()
		{
			$this->dataKluster = array();
			$this->rata2kluster = array();

		}

		public function ClusterValue($k, $dataID, $idResult, $teks){
			$this->jumlahKluster = count($dataID);
			$this->idTeks = $teks;

			$simpan = new dbClusterVariance;
			$simpan->k = $k;
			$simpan->id_hasil_kluster = $idResult;
			$simpan->save();
			$this->idSimpan = DB::table('cluster_variance')->max('id');
			echo "idSimpan = ".$this->idSimpan."<br />";

			$this->GetDocuments($dataID);
			
			$this->CalculateAverageCluster($this->dataKluster);

			$vw = $this->VarianWithin($this->dataKluster); echo "VW = ".$vw."<br />";
			$vb = $this->VarianBetween($this->rata2kluster); echo "VB = ".$vb."<br />";
			$v  = ($vw/$vb); echo "V = ".$v."<br />";

			$simpanLagi = dbClusterVariance::find($this->idSimpan);
			$simpanLagi->varian_within = $vw;
			$simpanLagi->varian_between = $vb;
			$simpanLagi->cluster_variance = $v;
			$simpanLagi->save();

			return $v;
		}

		public function VarianKluster($i, $kluster_i){
			$sum = 0.0;
			$nData = count($kluster_i);
			$kmeans = new Kmeans;

			if($nData>1){
				foreach ($kluster_i as $key => $dokumen) {
					if($this->idTeks=='a'){
						$di = json_decode($dokumen->nilai_tfidf_abstrak);
					}elseif ($this->idTeks=='ja') {
						$di = json_decode($dokumen->nilai_tfidf);
					}elseif ($this->idTeks=='j') {
						$di = json_decode($dokumen->nilai_tfidf_judul);
					}
					
					$dibar = $this->rata2kluster[$i];
					//$distance = $kmeans->CossineSimilarity($di, $dibar);
					$distance = $kmeans->Euclidean($di, $dibar);
					$sum += pow($distance, 2);
				}
				return ($sum/($nData-1));
			}
			else{
				return 0;
			}
		}

		public function VarianWithin($kluster){
			$Ndata = 0;
			foreach ($this->dataKluster as $key => $data) {
				$Ndata += count($data);
			}

			echo "----------------------- Varian Within -----------------------<br />";

			$sum = 0.0;
			for ($i=0; $i<$this->jumlahKluster ; $i++) { 
				$ni = count($kluster[$i]);
				echo "[k-".$i."] ";
				$vi = $this->VarianKluster($i, $kluster[$i]);
				echo $vi."<br />";
				$sum += ($ni-1)*$vi;
			}

			echo "-------------------------------------------------------------<br />";

			return $sum/($Ndata-$this->jumlahKluster);
		}

		public function VarianBetween($Rata2Centroid){
			$dbar = $this->AverageOfAverage($this->rata2kluster);

			echo "----------------------- Varian Between -----------------------<br />";

			$simpan = dbClusterVariance::find($this->idSimpan);
			$simpan->dmasing2 = json_encode($this->rata2kluster);
			$simpan->drata2 = json_encode($dbar);
			$simpan->save();

			$kmeans = new Kmeans;
			$sum = 0.0;

			for ($i=0; $i<$this->jumlahKluster ; $i++) { 
				$ni = count($this->dataKluster[$i]);
				//$distance = $kmeans->CossineSimilarity($Rata2Centroid[$i], $dbar);
				$distance = $kmeans->Euclidean($Rata2Centroid[$i], $dbar);
				echo "[k-".$i."] ".$distance."<br />";
				$sum += ($ni*pow($distance, 2));
			}
			return ($sum/($this->jumlahKluster-1));
		}

		public function AverageOfAverage($dataRata){
			$kamus = dbKamusJudul::all();
			$avg2 = (object) array();

			foreach ($kamus as $key => $kata) {
				$term = $kata->kata_dasar;
				$sum = 0.0;
				for ($i=0; $i<$this->jumlahKluster ; $i++) {
					$sum += $dataRata[$i]->$term;
				}
				$avg = ($sum/$this->jumlahKluster);
				$avg2->$term = $avg;
			}
			return $avg2;
		}

		public function CalculateAverageCluster($data){
			$kamus = dbKamusJudul::all();
			for ($i=0; $i<$this->jumlahKluster ; $i++) {
				$this->rata2kluster[$i] = (object) array();
				$n = count($data[$i]);
				foreach ($kamus as $key => $kata) {
					$term = $kata->kata_dasar;
					$sum = 0.0;
					if($n>0){
						foreach ($data[$i] as $key => $dokumen) {
							if($this->idTeks=='a'){
								$vectorDoc = json_decode($dokumen->nilai_tfidf_abstrak);
							}elseif ($this->idTeks=='ja') {
								$vectorDoc = json_decode($dokumen->nilai_tfidf);
							}elseif ($this->idTeks=='j') {
								$vectorDoc = json_decode($dokumen->nilai_tfidf_judul);
							}
							
							$sum += $vectorDoc->$term;
						}
						$avg = $sum/$n;
						$this->rata2kluster[$i]->$term = $avg;
					}
					else{
						$this->rata2kluster[$i]->$term = 0;
					}
				}
			}
		}

		public function GetDocuments($klusterID){
			for ($i=0; $i<$this->jumlahKluster ; $i++) { 
				$this->dataKluster[$i] = array();
				foreach ($klusterID[$i] as $key => $value) {
					$doc = dbDokumen::find($value);
					array_push($this->dataKluster[$i], $doc);
				}
			}
		}
	}
?>