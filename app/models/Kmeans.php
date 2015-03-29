<?php
	/**
	* 
	*/
	class Kmeans 
	{
		public $dokumenData;
		public $kamus;
		public $k_number;
		public $idcentroid;
		public $centroid;
		public $prevCrentroid;
		public $resultCluster;
		public $stopMarking;
		public $MAXiteration;
		public $counter;


		public function __construct()
		{
			$this->dokumenData = Dokumen::all();
			$this->kamus = KamusKata::all();
			$this->idcentroid = array();
			$this->centroid = array();
			$this->prevCrentroid = array();
			$this->resultCluster = array();
			$this->MAXiteration = 1;
			$this->counter = 0;
		}

		public function Clustering($k)
		{
			$this->RandomFirstCentroid($k);
			$this->prevCrentroid = $this->centroid;
			
			do{
				$this->ResetResult($k);
				for ($i=0; $i <7 ; $i++) { 
				//foreach ($this->dokumenData as $key => $dokumen) {
					$dokumen = $this->dokumenData[$i];
					$idx = $this->FindClosestCluster($dokumen);
					array_push($this->resultCluster[$idx], $dokumen);
				}
				//make $centroid like an empty array
				//$this->centroid=array();
				$this->CalculateMeanCentroid();
			}while ($this->CheckStoppingCriteria($this->prevCrentroid, $this->centroid));

		}
		
		public function RandomFirstCentroid($k)
		{
			$this->k_number = $k;
			$idcentroid = array();
			$n=count($this->dokumenData)-1;
			$i=0;
			do {
				$get = mt_rand(0,$n);
				if(!(in_array($get, $idcentroid))){
					array_push($idcentroid, $get);
					$doc = $this->dokumenData[$get];
					echo $get." - ".$this->dokumenData[$get]->nrp."<br />";
					$vectorCentroid = json_decode($doc->nilai_tfidf);
					array_push($this->centroid, $vectorCentroid);
					$i++;
				}
			} while ($i < $this->k_number);

			$this->idcentroid = $idcentroid;
			//return $this->centroid;
			
			//var_dump($this->centroid);
		}

		public function ResetResult($k){
			$this->resultCluster = array();
			for ($i=0; $i <$k ; $i++) { 
				$this->resultCluster[$i]=array();
			}
		}

		public function CheckStoppingCriteria($prev, $now)
		{
			$this->counter++;
			if($this->counter >= $this->MAXiteration){
				return false;
			}
			elseif (count($prev)==count($now) && count($now)!=0) {
				if($prev == $now)
					return false;
				else
					return true;
			}

		}

		public function FindClosestCluster($doc)
		{
			//echo(count($this->centroid));
			$cossineArray = array();
			$vectorDoc = json_decode($doc->nilai_tfidf);

			for ($i=0; $i < count($this->centroid); $i++) { 
				$cossineArray[$i] = $this->CossineSimilarity($this->centroid[$i], $vectorDoc);
			}

			return array_search(max($cossineArray), $cossineArray);
		}

		public function CossineSimilarity($v1, $v2)
		{
			$dot = $this->Similarity_DotProduct($v1, $v2);
			$mg1 = $this->Similarity_Magnitude($v1);
			$mg2 = $this->Similarity_Magnitude($v2);
			$cos = $dot/($mg1*$mg2);

			return $cos;
		}

		public function Similarity_DotProduct($vector1, $vector2)
		{
			$dotprod = 0.0;
			foreach ($this->kamus as $key => $kata) {
				$term = $kata->kata_dasar;
				$dotprod += (($vector1->$term)*($vector2->$term));
			}
			return (float) $dotprod;
		}

		public function Similarity_Magnitude($vector)
		{
			return (float) sqrt($this->Similarity_DotProduct($vector, $vector));
		}

		public function CalculateMeanCentroid()
		{
			for ($i=0; $i < count($this->centroid); $i++) { 
				$n = count($this->centroid[$i]);
				foreach ($this->kamus as $key => $kata) {
					$term = $kata->kata_dasar;
					$sum = 0.0;
					foreach ($this->resultCluster[$i] as $key => $dokumen) {
						$vectorDoc = json_decode($dokumen->nilai_tfidf);
						$sum += $vectorDoc->$term;
					}
					$avg = $sum/$n;
					$this->centroid[$i]->term = $avg;
				}
			}
		}
	}
?>