<?php
	/**
	* 
	*/
	class KmeansManualData 
	{
		public $data;
		public $k_number;
		public $n_number;
		public $variabel;
		public $centroid;
		public $prevCentroid;
		public $resultCluster;
		public $prevResultCluster;
		public $counter;	



		public $dokumenData;
		public $kamus;
		
		public $idcentroid;
		
		
		
		public $stopMarking;
		public $MAXiteration;
		
		public $dokumenID;
		public $gencen;
		public $idIndukResult;

		public $idTeks;
		public $idC;


		public function __construct()
		{
			
		}

		public function Clustering($k, $n, $dataC, $v, $cent)
		{	//k,n,data,v
			
			$this->data = $dataC;
			$this->n_number = $n;
			$this->variabel = $v;

			$this->centroid=array();
			$this->centroid = $cent;
			//var_dump($this->data);

			$counter = new TimeExecution;

			
			// //$this->RandomFirstCentroid($k);
			//$this->GenerateCentroidMean($k, $n, $v);
							
			do{
				$A = $counter->getTime();

				$this->prevCentroid = array();
				$this->prevCentroid = $this->centroid;

				$this->prevResultCluster = array();
				if($this->counter>0){
					$this->prevResultCluster = $this->resultCluster;
				}

				echo "Iterasi ".$this->counter."<br />";
				$this->ResetResult($k);
				for ($i=0; $i <$n ; $i++) { 
					$dokumen = $this->data[$i];
					$idx = $this->FindClosestCluster($dokumen);
					//echo $idx."<br />";
					array_push($this->resultCluster[$idx], $i);
				}

				$this->CalculateMeanCentroid();
				$Z = $counter->getTime();
				
			}while ($this->CheckStoppingCriteria($this->prevCentroid, $this->centroid, ($Z-$A), $n, $this->prevResultCluster, $this->resultCluster));
			
			echo "Nilai Cluster Variance = ".$this->ClusterVariance($k, $this->resultCluster, $v)."<br />";
			
		}
		
		/*public function RandomFirstCentroid($k)
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
					if($this->idTeks=='ja'){
						$vectorCentroid = json_decode($doc->nilai_tfidf);	
					}elseif ($this->idTeks=='a') {
						$vectorCentroid = json_decode($doc->nilai_tfidf_abstrak);
					}
					
					array_push($this->centroid, $vectorCentroid);
					$i++;
				}
			} while ($i < $this->k_number);

			$this->idcentroid = $idcentroid;
			//return $this->centroid;
			
			//var_dump($this->centroid);
		}*/


		public function ResetResult($k){
			$this->resultCluster = array();
			for ($i=0; $i <$k ; $i++) { 
				$this->resultCluster[$i]=array();
			}
		}

		public function CheckStoppingCriteria($prev, $now, $time, $n, $prevR, $R)
		{
			$this->counter++;
			// $this->SaveProcess($n, $time);
			if($this->counter >= $this->MAXiteration){
				return false;
			}
			elseif (count($prev)==count($now) && count($now)!=0) {
				if( ($prev==$now) && ($prevR==$R) )
					return false;
				else
					return true;
			}

		}

		public function FindClosestCluster($doc)
		{
			//echo(count($this->centroid));
			//echo "doc ".$doc->nrp." |";
			$distance = array();

			$vectorDoc = $doc;
			

			for ($i=0; $i < count($this->centroid); $i++) { 
				$distance[$i] = $this->Euclidean($this->centroid[$i], $vectorDoc);
				//echo $cossineArray[$i]."|";
			}

			$index = array_search(min($distance), $distance);
			//var_dump($cossineArray);
			//echo "<br />";
			//echo " -> ".$index."<br />";
			return $index;
		}

		public function Euclidean($v1, $v2){
			
			$fill = 0.0;
			

			for ($i=0; $i <$this->variabel ; $i++) { 
				$fill += pow(($v2[$i]-$v1[$i]), 2);
				//echo $cossineArray[$i]."|";
			}

			return sqrt($fill);
		}

		public function CossineSimilarity($v1, $v2)
		{
			$dot = $this->Similarity_DotProduct($v1, $v2);
			$mg1 = $this->Similarity_Magnitude($v1);
			$mg2 = $this->Similarity_Magnitude($v2);
			if(($mg1*$mg2)==0){
				return 0;
			}
			else {
				$cos = $dot/($mg1*$mg2);
				return $cos;
			}
		}

		public function Similarity_DotProduct($vector1, $vector2)
		{
			$dotprod = 0.0;
			for ($i=0; $i <$this->variabel ; $i++) { 
				$dotprod += (($vector1[$i])*($vector2[$i]));
			}
			
			return (float) $dotprod;
		}

		public function Similarity_Magnitude($vector)	
		{
			return (float) sqrt($this->Similarity_DotProduct($vector, $vector));
		}

		public function CalculateMeanCentroid()
		{
			
			$newCentroid = array();
			for ($i=0; $i < count($this->centroid); $i++) { 
				$newCentroid[$i] = array();
				$n = count($this->resultCluster[$i]);
				for ($j=0; $j <$this->variabel ; $j++) {
					$sum = 0.0;
					if($n > 0) {
						for ($k=0; $k <$n ; $k++) {
							$sum += $this->resultCluster[$i][$k][$j];
							//echo $vectorDoc->$term.",";
						}
						$avg = $sum/$n;
						//$this->centroid[$i]->$term = $avg;
						$newCentroid[$i][$j] = $avg;
					}
					else {
						$newCentroid[$i][$j] = $this->centroid[$i][$j];
					}
					
				}
				
				//echo "<br />-------------------------------------------------------------------------------<br />";
			}
			$this->centroid=array();
			$this->centroid = $newCentroid;
		}

		public function ClusterVariance($k, $dataID, $v){
			//get data hasil kluster
			$dataKluster = array();
			for ($i=0; $i<$k ; $i++) { 
				$dataKluster[$i] = array();
				foreach ($dataID[$i] as $key => $value) {
					$datax = $this->data[$value];
					array_push($dataKluster[$i], $datax);
					//echo $value."<br />";
				}
			}

			//var_dump($dataKluster);

			//hitung rataan masing-masing kluster
			$rata2kluster = array();
			for ($i=0; $i<$k ; $i++) {
				$rata2kluster[$i] = array();
				$n = count($dataKluster[$i]);
				for ($j=0; $j <$v ; $j++) { 
					$sum = 0.0;
					if($n>0){
						foreach ($dataKluster[$i] as $key => $dokumen) {
							$sum += $dokumen[$j];
						}
						$avg = $sum/$n;
						$rata2kluster[$i][$j] = $avg;
					}
					else{
						$rata2kluster[$i][$j] = 0;
					}
				}
			}
			//var_dump($rata2kluster);

			$vw = $this->CV_VarianWithin($dataKluster, $k, $rata2kluster); echo "VW = ".$vw."<br />";
			$vb = $this->CV_VarianBetween($dataKluster, $rata2kluster, $v, $k); echo "VB = ".$vb."<br />";
			$v  = ($vw/$vb); echo "V = ".$v."<br />";

			return $v;
		}

		public function CV_VarianKluster($i, $kluster_i, $rata2kluster){
			$sum = 0.0;
			$nData = count($kluster_i);
			//$kmeans = new Kmeans;

			if($nData>1){
				foreach ($kluster_i as $key => $di) {
					
					$dibar = $rata2kluster[$i];
					$distance = $this->Euclidean($di, $dibar);
					$sum += pow($distance, 2);
				}
				return ($sum/($nData-1));
			}
			else{
				return 0;
			}
		}

		public function CV_VarianWithin($dataKluster, $k, $rata2k){
			$Ndata = 0;
			foreach ($dataKluster as $key => $data) {
				$Ndata += count($data);
			}

			//echo "----------------------- Varian Within -----------------------<br />";

			$sum = 0.0;
			for ($i=0; $i<$k ; $i++) { 
				$ni = count($dataKluster[$i]);
				//echo "[k-".$i."] ";
				$vi = $this->CV_VarianKluster($i, $dataKluster[$i], $rata2k);
				//echo $vi."<br />";
				$sum += (($ni-1)*$vi);
			}

			//echo "-------------------------------------------------------------<br />";

			return $sum/($Ndata-$k);
		}

		public function CV_AverageOfAverage($rata2kluster, $v, $k){
			$avg2 = array();

			for ($j=0; $j <$v ; $j++) { 
				$sum = 0.0;
				for ($i=0; $i<$k ; $i++) {
					$sum += $rata2kluster[$i][$j];
				}
				$avg = ($sum/$k);
				$avg2[$j] = $avg;
			}
			return $avg2;
		}

		public function CV_VarianBetween($dataKluster ,$rata2k,  $v, $k){
			$dbar = $this->CV_AverageOfAverage($rata2k,  $v, $k);

			//echo "----------------------- Varian Between -----------------------<br />";

			$kmeans = new Kmeans;
			$sum = 0.0;

			for ($i=0; $i<$k ; $i++) { 
				$ni = count($dataKluster[$i]);
				$distance = $this->Euclidean($rata2k[$i], $dbar);
				//echo "[k-".$i."] ".$distance."<br />";
				$sum += ($ni*pow($distance, 2));
			}
			return ($sum/($k-1));
		}
	
		public function GenerateCentroidMean($k, $n, $v)
		{
			$this->k_number = $k;
			
				$centroid_arr = array();
				
				for ($i=0; $i <$k ; $i++) { 
					$centroid_arr[$i] = array();
				}

				for ($j=0; $j <$v ; $j++) { 
					$minmax = array();
					for ($i=0; $i < $n; $i++) {
						array_push($minmax , $this->data[$i][$j]);
					}
					$min = min($minmax);
					$max = max($minmax);
					$skala = ($max-$min)/$k;
					$tengah = $skala/2;
					for ($z=0; $z <$k ; $z++) { 
						$mean = ($z*$skala)+$tengah;
						$centroid_arr[$z][$j] = $mean;
					}
				}

				$this->centroid = $centroid_arr;
				var_dump($centroid_arr);
			
		}
	}
?>