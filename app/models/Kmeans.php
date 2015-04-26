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
		public $prevResultCluster;
		public $stopMarking;
		public $MAXiteration;
		public $counter;
		public $dokumenID;
		public $gencen;
		public $idIndukResult;


		public function __construct()
		{
			$this->dokumenData = Dokumen::all();
			$this->kamus = KamusKata::all();
			$this->gencen = CentroidGenerated::all();
			$this->idcentroid = array();
			$this->centroid = array();
			$this->prevCentroid = array();			
			$this->resultCluster = array();
			$this->prevResultCluster = array();
			$this->MAXiteration = 101;
			$this->counter = 0;

			$this->dokumenID = array();
			foreach ($this->dokumenData as $key => $doc) {
				array_push($this->dokumenID, $doc->nrp);
			}
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
			//$this->RandomFirstCentroid($k);
			$this->GenerateCentroidMean($k, $n);
			
			
			
			do{
				$A = $counter->getTime();

				$this->prevCentroid = array();
				$this->prevCentroid = $this->centroid;

				$this->prevResultCluster = array();
				if($this->counter>0){
					$this->prevResultCluster = $this->resultCluster;
				}
				// for ($i=0; $i < count($this->centroid); $i++) { 
				// 	//$this->prevCentroid[$i] = $this->centroid[$i];
				// 	$this->prevCentroid[$i] = array();
				// 	// foreach ($this->kamus as $key => $kata) {
				// 	// 	$term = $kata->kata_dasar;
				// 	// 	//$this->prevCentroid[$i]->$term = $this->centroid[$i]->$term;
				// 	// 	//echo $this->centroid[$i]->$term."<br />";
				// 	// 	var
				// 	// }
				// 	foreach ($variable as $key => $value) {
				// 		# code...
				// 	}
				// }
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
				
				// $this->prevResultCluster = array();
				// $this->prevResultCluster  = $this->resultCluster;

				$this->CalculateMeanCentroid();
				$Z = $counter->getTime();
				

				// for ($i=0; $i <count($this->centroid) ; $i++) { 
				// 	foreach ($this->kamus as $key => $kata) {
				// 		$term = $kata->kata_dasar;
				// 		echo $term." : ".$aaa[$i]->$term." -- ".$this->prevCentroid[$i]->$term." -- ".$this->centroid[$i]->$term."<br />";
				// 	}
				// }
				
			}while ($this->CheckStoppingCriteria($this->prevCentroid, $this->centroid, ($Z-$A), $n, $this->prevResultCluster, $this->resultCluster));
			
			
		}

		public function SaveProcess($ndoc, $time){
			
			$dt = new DateTime;
			$kmeansNow = KmeansResult::get();
			if($this->counter==1)
			{
				if(count($kmeansNow)==0){
					$this->idIndukResult = 1;
				}
				else{
					$maxID = DB::table('kmeans_result')->max('id_group');
					$this->idIndukResult = ($maxID+1);
				}
			}
			$result = array();
			for ($i=0; $i <$this->k_number ; $i++) { 
				$result[$i] = array();
				foreach ($this->resultCluster[$i] as $key => $rst) {
					array_push($result[$i], $rst->nrp);
				}
			}

			$saveKmeans = new KmeansResult();
			$saveKmeans->id_group = $this->idIndukResult;
			$saveKmeans->jumlah_kluster = $this->k_number;
			//$saveKmeans->id_kluster = json_encode($this->idcentroid);
			$saveKmeans->centroid_awal = $this->idcentroid;
			$saveKmeans->centroid_step = json_encode($this->prevCentroid);
			$saveKmeans->centroid_next = json_encode($this->centroid);
			$saveKmeans->jumlah_dokumen = $ndoc;
			$saveKmeans->hasil_kluster = json_encode($result);
			$saveKmeans->keterangan_iterasi = $this->counter."/".$this->MAXiteration;
			$saveKmeans->lama_eksekusi = $time;
			$saveKmeans->waktu_simpan = $dt->format('m-d-y H:i:s');
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

		public function CheckStoppingCriteria($prev, $now, $time, $n, $prevR, $R)
		{
			$this->counter++;
			$this->SaveProcess($n, $time);
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
			echo "doc ".$doc->nrp." |";
			$cossineArray = array();
			$vectorDoc = json_decode($doc->nilai_tfidf);

			for ($i=0; $i < count($this->centroid); $i++) { 
				$cossineArray[$i] = $this->CossineSimilarity($this->centroid[$i], $vectorDoc);
				echo $cossineArray[$i]."|";
			}

			$index = array_search(max($cossineArray), $cossineArray);
			//var_dump($cossineArray);
			//echo "<br />";
			echo " -> ".$index."<br />";
			return $index;
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

		public function GenerateCentroidMean($k, $n)
		{
			$this->k_number = $k;
			$dokumenIDx = array();
			$generated = false;
			for ($i=0; $i < $n; $i++) { 
				$doc = $this->dokumenData[$i];
				array_push($dokumenIDx, $doc->nrp);
			}
			//echo count($this->gencen)."string";
			if(count($this->gencen)==0){
				$generated = false;
				echo "<br /><br />*** CENTROID BARU ***<br /><br />";
			}
			else{
				foreach ($this->gencen as $key => $data) {
					//if(json_decode($data->dokumen)==$this->dokumenID && $data->k==$k && $data->jumlah_dokumen==$n){
					if(json_decode($data->dokumen)==$dokumenIDx && $data->k==$k && $data->jumlah_dokumen==$n){
						$getcentroid = array();
						$getcentroid = json_decode($data->centroid);
						$this->centroid = $getcentroid;
						//$this->idcentroid = json_decode($data->centroid);
						$this->idcentroid = $data->id;
						$generated = true;
						echo "<br /><br />*** CENTROID DATABASE ID=".$data->id." ***<br /><br />";
					}
				}
			}

			// if($generated)
			// 	echo "<br /><br />*** CENTROID DATABASE ID=".$data->id." ***<br /><br />";
			// else
			// 	echo "<br /><br />*** CENTROID BARU ***<br /><br />";

			
			if(!$generated){ 
				$centroid_arr = array();
				
				for ($i=0; $i <$k ; $i++) { 
					$centroid_arr[$i] = (object) array();
				}

				//$kata = KamusKata::find('muslim');
				//echo $kata->min_value." - ".$kata->max_value."<br />";
				// foreach ($this->kamus as $key => $kata) {
				// 	$term = $kata->kata_dasar;
				// 	$min = $kata->min_value;
				// 	$max = $kata->max_value;
				// 	$skala = ($max-$min)/$k;
				// 	$tengah = $skala/2;
				// 	//echo($tengah);

				// 	for ($i=0; $i <$k ; $i++) { 
				// 		$mean = ($i*$skala)+$tengah;
				// 		$centroid_arr[$i]->$term = $mean;
				// 		//echo $mean."<br />";
				// 	}

				// }

				foreach ($this->kamus as $key => $kata) {
					$term = $kata->kata_dasar;
					$minmax = array();

					for ($i=0; $i < $n; $i++) { 
						$doc = $this->dokumenData[$i];
						$vector = json_decode($doc->nilai_tfidf);
						array_push($minmax , $vector->$term);
					}
					$min = min($minmax);
					$max = max($minmax);
					$skala = ($max-$min)/$k;
					$tengah = $skala/2;
					//echo($tengah);

					for ($i=0; $i <$k ; $i++) { 
						$mean = ($i*$skala)+$tengah;
						$centroid_arr[$i]->$term = $mean;
						//echo $mean."<br />";
					}

					//echo($count++); echo " - "; echo($term); echo "<br />";
					//var_dump($minmax);
					//echo(count($minmax));
					//echo($term);

					// $updateMinMax = KamusKata::find($term);
					// $updateMinMax->min_value = min($minmax);
					// $updateMinMax->max_value = max($minmax);
					// $updateMinMax->save();
				}
	

				

				$saveCentroid = new CentroidGenerated;
				$saveCentroid->k = $k;
				$saveCentroid->jumlah_dokumen = $n;
				//$saveCentroid->dokumen = json_encode($this->dokumenID);
				$saveCentroid->dokumen = json_encode($dokumenIDx);
				$saveCentroid->centroid = json_encode($centroid_arr);
				$saveCentroid->save();

				//$data = CentroidGenerated::where('k','=',$k)->where('dokumen','=',json_encode($this->dokumenID))->where('jumlah_dokumen','=',$n)->get();
				$data = CentroidGenerated::where('k','=',$k)->where('dokumen','=',json_encode($dokumenIDx))->where('jumlah_dokumen','=',$n)->get();

				$this->centroid = json_decode($data[0]->centroid);
				//$this->centroid = $centroid_arr;
				$this->idcentroid = $data[0]->id;
				// foreach ($this->centroid as $key => $cen) {
				// 	echo "<br />";
				// 	for ($i=0; $i < 5; $i++) { 
				// 		echo $cen[$i]."<br />";
				// 	}
				// 	echo "----------------------------------<br />";
				// }
					
				// echo "<br /><br />--- First Centroid ---<br />";
				// var_dump($centroid_arr);
				// echo "<br />----------------------------<br /><br />";
			}
			
			
		}

		public function PickOfTerm($n)
		{
			$allTermPick50 =array();
			$count = 0;
			echo "BISMILLAH!! <br />";
			//var_dump($this->dokumenData);
			//$dokumen = Dokumen::find('5109100005');
			
			foreach ($this->dokumenData as $key => $dokumen) {	
				//$allTermPick50[$count] = array();
				
				$pick50 = array();
				$arr_tfidf = array();
				$urut = array();
				$keys = array();
				$vector = json_decode($dokumen->nilai_tfidf);
				for ($i=0; $i <count($this->kamus) ; $i++) { 
					$term = $this->kamus[$i]->kata_dasar;
					$arr_tfidf[$term] = $vector->$term;
				}
				//$urut = 
				asort($arr_tfidf);
				$pick50 = array_slice($arr_tfidf, -50);
				$keys = array_keys($pick50);

				
				
				//$allTermPick50[$count] = $keys;
				$count++;
				// echo $count." - ".$dokumen->nrp." - ".count($allTermPick50)."<br />";

				if($count == 1){
					//$diff = $keys;
					foreach ($keys as $key => $value) {										
							array_push($allTermPick50, $value);
					}
				}
				else{
					//$diff = array_diff($allTermPick50, $keys);	
					foreach ($keys as $key => $value) {
						if(in_array($value, $allTermPick50)){
							echo "false<br />";
						}
						else{

							
							array_push($allTermPick50, $value);
						}
					}
				}
				
				// foreach ($diff as $key => $value) {
				// 	array_push($allTermPick50, $value);
				// }
				
				//var_dump($allTermPick50);	
			}
			
			//var_dump(array_diff($allTermPick50,$a));
			echo "all : ".count($allTermPick50)."<br />";
			$term50 = array();
			$term50 = array_unique($allTermPick50);
			echo "distinct : ".count($term50)."<br />";

			// foreach ($allTermPick50 as $key => $value) {
			// 	echo $key." - ".count($value)."<br />";
			// }

			// var_dump($allTermPick50);

			/*$dokumen = Dokumen::find('5109100005');
			$vector = json_decode($dokumen->nilai_tfidf);

			foreach ($vector as $key => $tfidf) {
				//array_push($arr_tfidf, $tfidf);
			}

			
			

			//echo(array_search(0.040568598679134, $arr_tfidf));

			
			//var_dump($arr_tfidf);
			
			arsort($pick50);
			//var_dump($pick50);
			$en = json_encode($pick50);
			//var_dump(json_decode($en));
			//echo(array_search(0.040568598679134, $arr_tfidf));
			var_dump(array_keys($pick50));


			// $aaa = json_encode($arr_tfidf);
			// //var_dump($arr_tfidf);
			// $bbb = json_decode($aaa);
			// var_dump($bbb);*/

		}
	}
?>