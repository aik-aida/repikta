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
		
		function __construct()
		{
			$this->dataKluster = array();
			$this->rata2kluster = array();

		}

		public function ClusterValue($k, $dataID, $idResult){
			$this->jumlahKluster = count($dataID);
			$simpan = new DB_ClusterVariance;
			$simpan->k = $k;
			$simpan->id_hasil_kluster = $idResult;
			$simpan->save();
			$this->idSimpan = DB::table('cluster_variance')->max('id');

			$this->GetDocuments($dataID);
			
			$this->CalculateAverageCluster($this->dataKluster);

			$vw = $this->VarianWithin($this->dataKluster);
			$vb = $this->VarianBetween();
			return ($vw/$vb);
		}

		public function VarianKluster($i, $kluster_i){
			$sum = 0.0;
			$nData = count($kluster_i);
			$kmeans = new Kmeans;

			foreach ($kluster_i as $key => $dokumen) {
				$di = json_decode($dokumen->nilai_tfidf);
				$dibar = $this->rata2kluster[$i];
				$distance = $kmeans->CossineSimilarity($di, $dibar);
				$sum += pow($distance, 2);
			}
			return ($sum/($nData-1));
		}

		public function VarianWithin($kluster){
			$Ndata = count($kluster[0]) + count($kluster[1]) + count($kluster[2]);
			$sum = 0.0;
			echo $Ndata."<br />";
			for ($i=0; $i<$this->jumlahKluster ; $i++) { 
				$ni = count($kluster[$i]);
				$vi = $this->VarianKluster($i, $kluster[$i]);
				$sum += ($ni-1)*$vi;
			}

			return $sum/($Ndata-$this->jumlahKluster);
		}

		public function VarianBetween($Rata2Centroid){
			$dbar = $this->AverageOfAverage($this->rata2kluster);
			$kmeans = new Kmeans;
			$sum = 0.0;

			for ($i=0; $i<$this->jumlahKluster ; $i++) { 
				$ni = count($this->dataKluster[$i]);
				$distance = $kmeans->CossineSimilarity($Rata2Centroid[$i], $dbar);
				$sum += ($ni*pow($distance, 2));
			}
			return ($sum/($this->jumlahKluster-1));
		}

		public function AverageOfAverage($dataRata){
			$kamus = KamusKata::all();
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
			$kamus = KamusKata::all();
			for ($i=0; $i<$this->jumlahKluster ; $i++) {
				$this->rata2kluster[$i] = (object) array();
				$n = count($data[$i]);
				foreach ($kamus as $key => $kata) {
					$term = $kata->kata_dasar;
					$sum = 0.0;
					foreach ($data[$i] as $key => $dokumen) {
						$vectorDoc = json_decode($dokumen->nilai_tfidf);
						$sum += $vectorDoc->$term;
					}
					$avg = $sum/$n;
					$this->rata2kluster[$i]->$term = $avg;
				}
			}
		}

		public function GetDocuments($klusterID){
			for ($i=0; $i<$this->jumlahKluster ; $i++) { 
				$this->dataKluster[$i] = array();
				foreach ($klusterID[$i] as $key => $value) {
					$doc = Dokumen::find($value);
					array_push($this->dataKluster[$i], $doc);
				}
			}
		}
	}
?>