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
		public $prevCentroid;
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
			$this->prevCentroid = array();			
			$this->resultCluster = array();
			$this->MAXiteration = 101;
			$this->counter = 1;
		}

		public function Clustering($k, $n)
		{
			// for ($i=0; $i < 3; $i++) { 
			// 	$this->centroid[$i]=array('satu'=>'1', 'dua'=>'2', 'tiga'=>'3');
			// }
			// var_dump($this->centroid);
			// $this->prevCentroid = $this->centroid;
			// var_dump($this->prevCentroid);
			// for ($i=0; $i < 3; $i++) { 
			// 	$this->centroid[$i]=array('satu'=>'satu', 'dua'=>'dua', 'tiga'=>'tiga');
			// }
			// var_dump($this->prevCentroid);
			// var_dump($this->centroid);
			
			$counter = new TimeExecution;

			$this->centroid=array();
			$this->RandomFirstCentroid($k);
			
			
			do{
				$A = $counter->getTime();

				$this->prevCentroid = array();
				$this->prevCentroid = $this->centroid;
				/*for ($i=0; $i < count($this->centroid); $i++) { 
					//$this->prevCentroid[$i] = $this->centroid[$i];
					$this->prevCentroid[$i] = array();
					// foreach ($this->kamus as $key => $kata) {
					// 	$term = $kata->kata_dasar;
					// 	//$this->prevCentroid[$i]->$term = $this->centroid[$i]->$term;
					// 	//echo $this->centroid[$i]->$term."<br />";
					// 	var
					// }
					foreach ($variable as $key => $value) {
						# code...
					}
				}*/
				//$aaa = $this->centroid;
				//var_dump($this->prevCentroid);
				echo "Iterasi ".$this->counter."<br />";
				$this->ResetResult($k);
				for ($i=0; $i <$n ; $i++) { 
				//foreach ($this->dokumenData as $key => $dokumen) {
					$dokumen = $this->dokumenData[$i];
					$idx = $this->FindClosestCluster($dokumen);
					//echo $idx."<br />";
					array_push($this->resultCluster[$idx], $dokumen);
				}
				
				$this->CalculateMeanCentroid();
				$Z = $counter->getTime();
				$this->SaveProcess($n,($Z-$A));
				// for ($i=0; $i <count($this->centroid) ; $i++) { 
				// 	foreach ($this->kamus as $key => $kata) {
				// 		$term = $kata->kata_dasar;
				// 		echo $term." : ".$aaa[$i]->$term." -- ".$this->prevCentroid[$i]->$term." -- ".$this->centroid[$i]->$term."<br />";
				// 	}
				// }
			}while ($this->CheckStoppingCriteria($this->prevCentroid, $this->centroid));
			
		}

		public function SaveProcess($ndoc, $time){
			$result = array();
			for ($i=0; $i <$this->k_number ; $i++) { 
				$result[$i] = array();
				foreach ($this->resultCluster[$i] as $key => $rst) {
					array_push($result[$i], $rst->nrp);
				}
			}

			$saveKmeans = new KmeansTime();
			$saveKmeans->jumlah_kluster = $this->k_number;
			$saveKmeans->id_kluster = json_encode($this->idcentroid);
			$saveKmeans->jumlah_dokumen = $ndoc;
			$saveKmeans->hasil_kluster = json_encode($result);
			$saveKmeans->jumlah_iterasi = $this->counter."/".$this->MAXiteration;
			$saveKmeans->lama_eksekusi = $time;
			$saveKmeans->save();
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
			//echo "doc ".$doc->nrp;
			$cossineArray = array();
			$vectorDoc = json_decode($doc->nilai_tfidf);

			for ($i=0; $i < count($this->centroid); $i++) { 
				$cossineArray[$i] = $this->CossineSimilarity($this->centroid[$i], $vectorDoc);
			}
			//var_dump($cossineArray);
			//echo "<br />";
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
			// echo "string<br />";
			// echo count($this->centroid)." : ";
			// foreach ($this->resultCluster as $key => $value) {
			// 	echo count($value).",";
			// }
			// echo "string<br />";
			$newCentroid = array();
			for ($i=0; $i < count($this->centroid); $i++) { 
				$newCentroid[$i] = (object) array();
				$n = count($this->resultCluster[$i]);
				foreach ($this->kamus as $key => $kata) {
					$term = $kata->kata_dasar;
					$sum = 0.0;
					// echo $n.".".$term." : ";
					// echo "cls ".$i." [".$this->counter."] ";
					// echo $this->prevCentroid[$i]->$term." == ";
					// echo $this->centroid[$i]->$term." -> (";
					if($n > 0) {
						foreach ($this->resultCluster[$i] as $key => $dokumen) {
							$vectorDoc = json_decode($dokumen->nilai_tfidf);
							$sum += $vectorDoc->$term;
							//echo $vectorDoc->$term.",";
						}
						$avg = $sum/$n;
						//$this->centroid[$i]->$term = $avg;
						$newCentroid[$i]->$term = $avg;
					}
					else {
						$newCentroid[$i]->$term = $this->centroid[$i]->$term;
					}
					//echo ") = ".$sum." / ".$n. " ==>> ";
					//echo "stringhitungcek<br />";
					//echo $term." : ".$newCentroid[$i]->$term."><".$this->prevCentroid[$i]->$term."<br />";
				}
				
				//echo "<br />-------------------------------------------------------------------------------<br />";
			}
			$this->centroid=array();
			$this->centroid = $newCentroid;
		}
	}
?>