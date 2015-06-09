<?php
	/**
	* 
	*/
	class LdaGibbsSampling 
	{
		public $vocab;
		public $Nterm;
		public $corpus;
		public $Mdoc;
		public $docName;
		public $Ktopic;

		public $alpha;
		public $beta;
		public $phi;
		public $theta;
		public $phisum;
		public $thetasum;

		public $sampleLAG;
		public $burnIN;
		public $ITERATIONS;
		public $numstat;

		public $zTopic;
		public $nw;
		public $nd;
		public $nwsum;
		public $ndsum;

		


		public function __construct() {
			$this->vocab = $this->GetTermVocab();

			$this->beta = 0.01;
			$this->sampleLAG = 2;	//100 //2
			$this->ITERATIONS = 2000;	//100	//2000
			$this->burnIN = 10;	//50	//100
		}

		public function TopicExtraction($k, $list_doc, $id, $ke, $grup, $no_percobaan){
			$counter = new TimeExecution;
			$awal = $counter->getTime();

			$this->FillMatrixCorpus($list_doc);
			
			
			$this->Ktopic = $k;
			$this->alpha = (50/$this->Ktopic);
			//$this->beta = (200/$this->Nterm);
			$this->PrepareVariabel($this->Mdoc, $this->Nterm, $this->Ktopic);
			$randTopic = $this->RandomTopicFirst($this->Mdoc, $this->corpus, $this->Ktopic);


			
			//echo "ITERASI";
			//iterasi penetapan topik yang lebih tepat dengan metode komulatif multinomial sampling (random)
			for ($i=0; $i<$this->ITERATIONS ; $i++) { 
				for ($m=0; $m<$this->Mdoc ; $m++) { 
					$N = count($this->zTopic[$m]);
					//echo "------------------------------ Dokumen ".($m+1)." banyak kata ".$N."<br />";
					for ($n=0; $n<$N ; $n++) { 
						$new_topic = $this->UpdateTopicAssignment($m, $n);
						$this->zTopic[$m][$n] = $new_topic;	
					}
				}

				if( ($i > $this->burnIN) && ( $this->sampleLAG > 0 ) && ( $i % $this->sampleLAG == 0) ){
					$this->UpdateSUM();
				}
			}

			$this->CalculateTheta();
			$this->CalculatePhi();
			

			$akhir = $counter->getTime();
			$lama = ($akhir-$awal);
			
			$simpan = new dbLdaSave;
			$simpan->percobaan_ke = $no_percobaan;
			$simpan->group = $grup;
			$simpan->id_kluster = $id;
			$simpan->kluster_ke = $ke;
			$simpan->k_topik = $this->Ktopic;
			$simpan->m_dokumen = $this->Mdoc;
			$simpan->n_term = $this->Nterm;
			$simpan->daftar_dokumen = json_encode($this->docName);
			$simpan->matriks_term = json_encode($this->vocab);
			$simpan->matriks_dokumen = json_encode($this->corpus);
			$simpan->matriks_Ztopik = json_encode($randTopic);
			$simpan->alpha = $this->alpha;
			$simpan->beta = $this->beta;
			$simpan->BURN_IN = $this->burnIN;
			$simpan->SAMPLE_LAG = $this->sampleLAG;
			$simpan->ITERATIONS = $this->ITERATIONS;
			$simpan->matriks_phi = json_encode($this->phi);
			$simpan->matriks_theta = json_encode($this->theta);
			$simpan->lama_eksekusi = $lama;
			$simpan->save();

		}

		public function CalculatePhi() {
			//merupakan probabilitas Term pada Vocab terhadap Topik, maka matrix phisum sebesar N x K
			// for ($n=0; $n<$this->Nterm ; $n++) { 
			// 	for ($k=0; $k<$this->Ktopic ; $k++) { 
			// 		if($this->sampleLAG>0){
			// 			$this->phi[$n][$k] = $this->phisum[$k][$n]/$this->numstat;
			// 		}
			// 		else {
			// 			$a = $this->nw[$n][$k]+$this->beta;
			// 			$b = $this->nwsum[$k]+($this->Nterm*$this->beta);
			// 			$this->phi[$n][$k] = $a/$b;
			// 		}
			// 	}
			// }

			for ($k=0; $k < $this->Ktopic; $k++) {
	            for ($n=0; $n < $this->Nterm; $n++) {
	                if ($this->sampleLAG>0) {
	                	$this->phi[$k][$n] = $this->phisum[$k][$n] / $this->numstat;
	                }
	                else {
	                	$a = $this->nw[$n][$k] + $this->beta;
	                	$b = $this->nwsum[$k] + ($this->Nterm * $this->beta);
	                	$this->phi[$k][$n] = $a/$b;
	                }
	            }
	        }
	        
	        $k_length = count($this->phi);
	        $n_length = count($this->phi[0]);

	        $trans = array();
	        for ($i=0; $i <$n_length ; $i++) { 
	        	$trans[$i] = array();
	        }
	        for ($i=0; $i <$n_length ; $i++) { 
	        	$trans[$i] = array();
	        	for ($j=0; $j <$k_length ; $j++) { 
	        		$trans[$i][$j] = $this->phi[$j][$i];
	        	}
	        }
	        unset($this->phi);
			$this->phi = array();
	        $this->phi = $trans;
		}

		public function CalculateTheta() {
			//merupakan probabilitas Topik terhadap Dokumen, maka matrix thetasum sebesar M x K
			for ($m=0; $m<$this->Mdoc ; $m++) { 
				for ($k=0; $k<$this->Ktopic ; $k++) { 
					if($this->sampleLAG>0){
						$this->theta[$m][$k] = $this->thetasum[$m][$k]/$this->numstat;
					}
					else {
						$c = $this->nd[$m][$k] + $this->alpha;
						$d = $this->ndsum[$m] + ($this->Ktopic * $this->alpha);
						$this->theta[$m][$k] = $c/$d;
					}
				}
			}
		}

		public function UpdateTopicAssignment($m, $n) {
			// Multinomial Sampling
			// Sample Full Conditional
			
			//var_dump($this->zTopic);
			$topic = $this->zTopic[$m][$n];
			$idTerm = $this->corpus[$m][$n];
			//$new_topic = $last_topic;
			if($m<5 && $n<5){
				// echo "dokumen[".$m."] term[".$n.",".$idTerm."] topik_lama=".$topic."<br />";
				// echo "nw=".$this->nw[$idTerm][$topic]." , ";
				// echo "nd=".$this->nd[$m][$topic]." , ";
				// echo "nwsum=".$this->nwsum[$topic]." , ";
				// echo "ndsum=".$this->ndsum[$m]."<br />";
			}
			
			

			//membuat sementara item Z[m][n] menghilang
			$this->nw[$idTerm][$topic]--;
			$this->nd[$m][$topic]--;
			$this->nwsum[$topic]--;
			$this->ndsum[$m]--;

			// if($m<5 && $n<5){
			// 	echo "nw=".$this->nw[$idTerm][$topic]." , ";
			// 	echo "nd=".$this->nd[$m][$topic]." , ";
			// 	echo "nwsum=".$this->nwsum[$topic]." , ";
			// 	echo "ndsum=".$this->ndsum[$m]."<br />";
			// }
			
			//Multinomial Sampling dengan Metode Kumulatif
			$P = array();
			for ($k=0; $k<$this->Ktopic ; $k++) { 
				//$a = $idTerm+$this->beta;
				$a = $this->nw[$idTerm][$k] + $this->beta;
				$b = $this->nwsum[$k] + ($this->Nterm * $this->beta);
				$c = $this->nd[$m][$k] + $this->alpha;
				$d = $this->ndsum[$m] + ($this->Ktopic * $this->alpha);
				$P[$k] = ($a/$b)*($c/$d);

				// if($m<5 && $n<5){
				// 	echo "P[".$k."] = ".number_format($P[$k], 10)."<br />";
				// }
			}
			//Parameter Multinomial Kumulatif
			//for ($k=1; $k<$this->Ktopic ; $k++) { 
			for ($k=1; $k<count($P) ; $k++) { 
				$P[$k] += $P[$k-1];
			}

			// if($m<5 && $n<5){
			// 	echo "--- Parameter Multinomial Kumulatif ---<br />";
			// 	for ($k=0; $k<$this->Ktopic ; $k++) { 
			// 		echo "P[".$k."] = ".number_format($P[$k],10)."<br />";
			// 	}
			// }

			//random sampling dikarenakan bentuk P[] yang tidak normal
			$banyakK = ($this->Ktopic-1);

			$random = mt_rand() / mt_getrandmax();
			$value = $random * $P[$banyakK];
			//$value = mt_rand(0,$P[$banyakK]);

			// if($m<5 && $n<5){
			// 	echo "NILAI value : ".$value."<br />";
			// }

			for ($k=0; $k<$this->Ktopic ; $k++) { 
				if($value < $P[$k]){
					$topic = $k;
					break;
				}
			}

			// if($m<5 && $n<5){
			// 	echo "------------------------------------------------------------------------topik_baru=".$topic."<br />";
			// }

			//mengambalikan keberadaan item Z[m][n] dalam corpus
			$this->nw[$idTerm][$topic]++;
			$this->nd[$m][$topic]++;
			$this->nwsum[$topic]++;
			$this->ndsum[$m]++;

			return $topic;
		}

		public function UpdateSUM() {
			for ($m=0; $m<$this->Mdoc ; $m++) { 
				for ($k=0; $k<$this->Ktopic ; $k++) { 
					$c = $this->nd[$m][$k] + $this->alpha;
					$d = $this->ndsum[$m] + ($this->Ktopic * $this->alpha);
					$this->thetasum[$m][$k] += ($c/$d);
				}
			}
			for ($k=0; $k<$this->Ktopic ; $k++) { 
				for ($n=0; $n<$this->Nterm ; $n++) { 
					$a = $this->nw[$n][$k] + $this->beta; 
					$b = $this->nwsum[$k] + ($this->Nterm * $this->beta);
					$this->phisum[$k][$n] += ($a/$b);
				}
			}
			$this->numstat++;
		}

		public function RandomTopicFirst($M, $C, $K){
			//echo "RandomTopicFirst<br />";
			$k = ($K-1);
			for ($m=0; $m<$M ; $m++) { 
				$N = count($C[$m]);

				for ($n=0; $n<$N ; $n++) { 
					$idTerm = $C[$m][$n];

					$idTopic = mt_rand(0,$k);
					$this->zTopic[$m][$n] = $idTopic;	//menentukan random topik untuk masing-masing kata pada masing-masing dokumen
					$this->nw[$idTerm][$idTopic]++;		//menambahkan jumlah term idTerm yang bertopik idTopic
					$this->nd[$m][$idTopic]++;			//menambahkan jumlah topik idTopic yang muncul pada dokumen m
					$this->nwsum[$idTopic]++;			//menambahkan jumlah topik idTopic yang muncul dalam corpus dokumen

					if($n<50){
						//echo $idTopic." , ";
					}
				}
				//echo "<br />";
				$this->ndsum[$m] = $N;	//menambahkan banyak kemunculan topic untuk setiap dokumen m		
			}
			// echo "<br />";
			// echo "-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br />";
			// echo "<br />";

			
			return $this->zTopic;
		}

		public function PrepareVariabel($M, $N, $K) {
			$this->nwsum = array();
			$this->ndsum = array();

			$this->nw = array();
			$this->nd = array();
			$this->zTopic = array();
			$this->theta = array();
			$this->thetasum = array();
			$this->phi = array();
			$this->phisum = array();

			for ($n=0; $n<$N ; $n++) { 
				$this->nw[$n] = array();
				for ($k=0; $k<$K ; $k++) { 
					$this->nw[$n][$k] = 0;
				}
			}

			for ($m=0; $m<$M ; $m++) { 
				$this->nd[$m] = array();
				$this->ndsum[$m] = 0;

				$this->zTopic[$m] = array();
				$this->theta[$m] = array();
				for ($k=0; $k<$K ; $k++) { 
					$this->nd[$m][$k] = 0;
				}
			}

			for ($k=0; $k<$K ; $k++) { 
				$this->phi[$k] = array();
				$this->nwsum[$k] = 0;
			}

			if($this->sampleLAG > 0){
				for ($m=0; $m<$M ; $m++) {
					$this->thetasum[$m] = array();
					for ($k=0; $k<$K ; $k++) { 
						$this->thetasum[$m][$k] = 0;

					}
				}

				for ($k=0; $k<$K ; $k++) { 
					$this->phisum[$k] = array();
					for ($n=0; $n<$N ; $n++) { 
						$this->phisum[$k][$n] = 0;
					}
				}

				$this->numstat = 0;
			}
		}

		public function GetTermVocab() {
			$this->vocab = array();
			$temp_vocab = array();
			$kamuskata = dbKamusKata::get();

			foreach ($kamuskata as $key => $datakata) {
				array_push($temp_vocab, $datakata->kata_dasar);
			}

			$this->Nterm = count($temp_vocab);
			return $temp_vocab;
		}

		public function FillMatrixCorpus($arrDOC) {
			// $corp = Dokumen::get();
			$this->corpus = array();
			$this->docName = array();
			$M = count($arrDOC);

			for ($m=0; $m < $M; $m++) { 
				array_push($this->docName, $arrDOC[$m]);
				$dokumen = dbDokumen::find($arrDOC[$m]);
				$this->corpus[$m] = array();

				$nilai_tfidf = json_decode($dokumen->nilai_tfidf);
				$tfidf_sorted = array();
				foreach ($nilai_tfidf as $key => $value) {
					$tfidf_sorted[$key] = $value;
				}
				arsort($tfidf_sorted);

				// $N = 50;
				// $top = array_slice($tfidf_sorted,0,$N);
				// $katakata = array_keys($top);

				// $katakata = array();
				// foreach ($tfidf_sorted as $key => $value) {
				// 	if($value >= 0.003){
				// 		array_push($katakata, $key);
				// 	}
				// 	if($value < 0.003){
				// 		break;
				// 	}
				// }

				$katakata = explode(' ', $dokumen->abstrak_af_preproc);
				$N = count($katakata);

				for ($n=0; $n < $N; $n++) { 
					$idx = array_search(trim($katakata[$n]), $this->vocab);
					if(strlen(trim($katakata[$n]))!=0){
						array_push($this->corpus[$m], $idx);
						//echo trim($katakata[$n])."<br />";
					}
					
				}
				//echo "banyak kata dokumen ".($m+1)." adalah ".count($this->corpus[$m])."<br />";
			}

			$this->Mdoc = $M;
		}
	}
?>