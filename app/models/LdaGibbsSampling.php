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
			$this->FillMatrixCorpus();

			$this->beta = 0.01;
			$this->sampleLAG = 2;	//100 //2
			$this->ITERATIONS = 2000;	//100	//2000
			$this->burnIN = 100;	//50	//100
		}

		public function TopicExtraction($k){
			$this->Ktopic = $k;
			$this->alpha = (50/$this->Ktopic);
			$this->PrepareVariabel($this->Mdoc, $this->Nterm, $this->Ktopic);
			$this->RandomTopicFirst($this->Mdoc, $this->corpus, $this->Ktopic);

			//iterasi penetapan topik yang lebih tepat dengan metode komulatif multinomial sampling (random)
			for ($i=0; $i<$this->ITERATIONS ; $i++) { 
				for ($m=0; $m<$this->Mdoc ; $m++) { 
					$N = count($this->zTopic[$m]);
					//echo $N."<br />";
					for ($n=0; $n<$N ; $n++) { 
						if($this->corpus[$m][$n]!=-1){
							$new_topic = $this->UpdateTopicAssignment($m, $n);
							$this->zTopic[$m][$n] = $new_topic;	
						}
					}
				}

				if( ($i > $this->burnIN) && ( $this->sampleLAG > 0 ) && ( $i % $this->sampleLAG == 0) ){
					$this->UpdateSUM();
				}
			}

			$this->CalculateTheta();
			$this->CalculatePhi();
		}

		public function CalculatePhi() {
			//merupakan probabilitas Term pada Vocab terhadap Topik, maka matrix phisum sebesar N x K
			for ($n=0; $n<$this->Nterm ; $n++) { 
				for ($k=0; $k<$this->Ktopic ; $k++) { 
					if($this->sampleLAG>0){
						$this->phi[$n][$k] = $this->phisum[$k][$n]/$this->numstat;
					}
					else {
						$a = $this->nw[$n][$k]+$this->beta;
						$b = $this->nwsum[$k]+($this->Nterm*$this->beta);
						$this->phi[$n][$k] = $a/$b;
					}
				}
			}
		}

		public function CalculateTheta() {
			//merupakan probabilitas Topik terhadap Dokumen, maka matrix thetasum sebesar M x K
			for ($m=0; $m<$this->Mdoc ; $m++) { 
				for ($k=0; $k<$this->Ktopic ; $k++) { 
					if($this->sampleLAG>0){
						$this->theta[$m][$k] = $this->thetasum[$m][$k]/$this->numstat;
					}
					else {
						$a = $this->nd[$m][$k]+$this->alpha;
						$b = $this->ndsum[$m]+($this->Ktopic*$this->alpha);
						$this->theta[$m][$k] = $a/$b;
					}
				}
			}
		}

		public function UpdateTopicAssignment($m, $n) {
			// Multinomial Sampling
			// Sample Full Conditional
			
			//var_dump($this->zTopic);
			$last_topic = $this->zTopic[$m][$n];
			$idTerm = $this->corpus[$m][$n];
			$new_topic = $last_topic;
			
			//membuat sementara item Z[m][n] menghilang
			$this->nw[$idTerm][$last_topic]--;
			$this->nd[$m][$last_topic]--;
			$this->nwsum[$last_topic]--;
			$this->ndsum[$m]--;
			
			//Multinomial Sampling dengan Metode Kumulatif
			$P = array();
			for ($k=0; $k<$this->Ktopic ; $k++) { 
				$a = $idTerm+$this->beta;
				$b = $this->nwsum[$k]+($this->Nterm*$this->alpha);
				$c = $this->nd[$m][$k]+$this->alpha;
				$d = $this->ndsum[$m]+($this->Ktopic*$this->alpha);
				$P[$k] = ($a/$b)*($c/$d);
			}
			//Parameter Multinomial Kumulatif
			for ($k=1; $k<$this->Ktopic ; $k++) { 
				$P[$k] += $P[$k-1];
			}
			//random sampling dikarenakan bentuk P[] yang tidak normal
			$k = ($this->Ktopic-1);
			$value = mt_rand(0,$P[$k]);
			for ($k=0; $k<$this->Ktopic ; $k++) { 
				if($value < $P[$k]){
					$new_topic = $k;
					break;
				}
			}

			//mengambalikan keberadaan item Z[m][n] dalam corpus
			$this->nw[$idTerm][$new_topic]++;
			$this->nd[$m][$new_topic]++;
			$this->nwsum[$new_topic]++;
			$this->ndsum[$m]++;

			return $new_topic;
			
		}

		public function UpdateSUM() {
			for ($m=0; $m<$this->Mdoc ; $m++) { 
				for ($k=0; $k<$this->Ktopic ; $k++) { 
					$a = $this->nd[$m][$k]+$this->alpha;
					$b = $this->ndsum[$m]+($this->Ktopic*$this->alpha);
					$this->thetasum[$m][$k] += $a/$b;
				}
				for ($k=0; $k<$this->Ktopic ; $k++) { 
					for ($n=0; $n<$this->Nterm ; $n++) { 
						$c = $this->nw[$n][$k]+$this->beta;
						$d = $this->nwsum[$k]+($this->Nterm*$this->beta);
						$this->phisum[$k][$n] += $c/$d;
					}
				}
				$this->numstat++;
			}
		}

		public function RandomTopicFirst($M, $C, $K){
			
			$k = ($K-1);
			for ($m=0; $m<$M ; $m++) { 
				$N = count($C[$m]);
				$bertopik = 0;

				for ($n=0; $n<$N ; $n++) { 
					$idTerm = $C[$m][$n];
					if($idTerm != -1) {
						$idTopic = mt_rand(0,$k);
						$this->zTopic[$m][$n] = $idTopic;	//menentukan random topik untuk masing-masing kata pada masing-masing dokumen
						$this->nw[$idTerm][$idTopic]++;		//menambahkan jumlah term idTerm yang bertopik idTopic
						$this->nd[$m][$idTopic]++;			//menambahkan jumlah topik idTopic yang muncul pada dokumen m
						$this->nwsum[$idTopic]++;			//menambahkan jumlah topik idTopic yang muncul dalam corpus dokumen
						$bertopik++;
					}
					else{
						$this->zTopic[$m][$n] = -1;	
					}
				}

				$this->ndsum[$m] = $bertopik;	//menambahkan banyak kemunculan topic untuk setiap dokumen m
			}
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
			$kamuskata = KamusKata::get();

			foreach ($kamuskata as $key => $datakata) {
				array_push($temp_vocab, $datakata->kata_dasar);
			}

			$this->Nterm = count($temp_vocab);
			return $temp_vocab;
		}

		public function FillMatrixCorpus() {
			$corp = Dokumen::get();
			$this->corpus = array();
			$this->docName = array();
			$M = count($corp);

			for ($m=0; $m < $M; $m++) { 
				array_push($this->docName, $corp[$m]->nrp);
				$this->corpus[$m] = array();
				$katakata = explode(' ', $corp[$m]->abstrak_af_preproc);
				$N = count($katakata);
				for ($n=0; $n < $N; $n++) { 
					$idx = array_search($katakata[$n], $this->vocab);
					if($idx == NULL) {	//jika kata tidak ada dalam kamus -> 'spasi' , maka id=-1
						array_push($this->corpus[$m], -1);	
					}else{
						array_push($this->corpus[$m], $idx);	
					}
					
				}
			}

			$this->Mdoc = count($this->corpus);
		}
	}
?>