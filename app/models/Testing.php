<?php
	/**
	* 
	*/
	class Testing
	{
		public $kamus;
		public function __construct(){
			$this->kamus = dbKamusKata::all();
		}

		public function TfIdf($bobot_judul, $bobot_abstrak)
		{
			$dokumens = dbDokumen::where('training','=',false)->get();
			$words = dbKamusKata::all();
			$judul_words = dbKamusJudul::all();
			foreach ($dokumens as $key => $dokumen) {
				$tfvector = (object) array();
				foreach ($words as $key => $kata) {
					$term = $kata->kata_dasar;
					$nword = substr_count($dokumen->abstrak_af_preproc, ' '.$term.' ');
					$nall = str_word_count($dokumen->abstrak_af_preproc,0);
					$tfvector->$term = (float)((float)$nword/(float)$nall);			
				}

				$judul_tfvector = (object) array();
				foreach ($judul_words as $key => $kata) {
					$term = $kata->kata_dasar;
					$nword = substr_count($dokumen->judul_af_preproc, ' '.$term.' ');
					$nall = str_word_count($dokumen->judul_af_preproc,0);
					$judul_tfvector->$term = (float)((float)$nword/(float)$nall);			
				}

				$doc = dbDokumen::find($dokumen->nrp);
				$doc->nilai_tf_abstrak = json_encode($tfvector);
				$doc->nilai_tf_judul = json_encode($judul_tfvector);
				$doc->save();
				echo "hitung TF ".$dokumen->nrp."<br />";
			}

			$dokumens1 = dbDokumen::where('training','=',false)->get();
			foreach ($dokumens1 as $key => $dokumen) {
				$tfidf = (object) array();
				$tf = json_decode($dokumen->nilai_tf_abstrak);

				$judul_tfidf = (object) array();
				$judul_tf = json_decode($dokumen->nilai_tf_judul);

				foreach ($words as $key => $kata) {
					$term = $kata->kata_dasar;
					$tfidf->$term = (float)((float)($tf->$term)*(float)($kata->idf));
				}

				foreach ($judul_words as $key => $kata) {
					$term = $kata->kata_dasar;
					$judul_tfidf->$term = (float)((float)($judul_tf->$term)*(float)($kata->idf));
				}

				$doc = dbDokumen::find($dokumen->nrp);
				$doc->nilai_tfidf_abstrak = json_encode($tfidf);
				$doc->nilai_tfidf_judul = json_encode($judul_tfidf);
				$doc->save();
				echo "hitung TF-IDF ".$dokumen->nrp."<br />";
			}

			$dokumens2 = dbDokumen::where('training','=',false)->get();
			foreach ($dokumens2 as $key => $dokumen) {
				$tfidf = (object) array();
				$tfidf_j = json_decode($dokumen->nilai_tfidf_judul);
				$tfidf_a = json_decode($dokumen->nilai_tfidf_abstrak);
				$cn = 0;
				foreach ($words as $key => $kata) {
					$term = $kata->kata_dasar;

					if(array_key_exists($term, $tfidf_j)){
						$tfidf->$term = ($bobot_judul*$tfidf_j->$term)+($bobot_abstrak*$tfidf_a->$term);
					}else{
						$tfidf->$term = ($bobot_abstrak*$tfidf_a->$term);
					}
				}
				
				$doc = dbDokumen::find($dokumen->nrp);
				$doc->nilai_tfidf = json_encode($tfidf);
				$doc->save();
				echo "Pembobotan ".$dokumen->nrp."<br />";
			}
		}

		public function TopikVektor($kluster, $kumpulan, $id_hasil_lda)
		{
			$jumlah = count($kumpulan);
			//echo($jumlah);

			$lda_result = dbLdaSave::where('group','=',$id_hasil_lda)
								->where('kluster_ke','=',$kluster)->get();
			$list_nrp = json_decode($lda_result[0]->daftar_dokumen);		//DAFTAR DOKUMEN PADA KLUSTER TERPILIH
			$theta_matrix = json_decode($lda_result[0]->matriks_theta);		//THETA LDA PADA KLUSTER TERPILIH
			$phi_matrix = json_decode($lda_result[0]->matriks_phi);			//PHI LDA PADA KLUSTER TERPILIH
			$k = $lda_result[0]->k_topik;									//JUMLAH TOPIK PADA KLUSTER TERPILIH
			$nterm = $lda_result[0]->n_term;								//BANYAK TERM LDA PADA KLUSTER TERPILIH
			$list_term = json_decode($lda_result[0]->matriks_term);			//DAFTAR TERM LDA PADA KLUSTER TERPILIH
			$vectorTopik = (object) array();
			if($jumlah==1){
				for ($n=0; $n <$nterm ; $n++) { 
					$vectorTopik->$list_term[$n] = $phi_matrix[$n][$kumpulan[0]];
				}
				//var_dump($vectorTopik);
			}
			else{
				for ($n=0; $n <$nterm ; $n++) { 
					$sum = 0.0;
					foreach ($kumpulan as $key => $idtopic) {
						$sum += $phi_matrix[$n][$idtopic];
					}
					$vectorTopik->$list_term[$n] = $sum/$jumlah;
				}
				//var_dump($vectorTopik);
			}
			return $vectorTopik;
		}

		public function CosineValRekomendasi($vTopik, $vDokumen)
		{
			return $this->CossineSimilarity($vTopik, $vDokumen);
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

		public function PerplexityLDA($id_hasil_lda, $group, $kluster)
		{
			$lda_result = dbLdaSave::where('percobaan_ke','=',$id_hasil_lda)
								->where('kluster_ke','=',$kluster)
								->where('group','=',$group)->get();

			// $banyak = count($lda_result);
			// echo "banyak : ".$banyak."<br />";
			// for ($i=0; $i <$banyak ; $i++) {
				$list_nrp = json_decode($lda_result[0]->daftar_dokumen);		//DAFTAR DOKUMEN PADA KLUSTER TERPILIH
				$theta_matrix = json_decode($lda_result[0]->matriks_theta);		//THETA LDA PADA KLUSTER TERPILIH
				$phi_matrix = json_decode($lda_result[0]->matriks_phi);			//PHI LDA PADA KLUSTER TERPILIH
				$kTopik = $lda_result[0]->k_topik;									//JUMLAH TOPIK PADA KLUSTER TERPILIH
				$nterm = $lda_result[0]->n_term;								//BANYAK TERM LDA PADA KLUSTER TERPILIH
				$list_term = json_decode($lda_result[0]->matriks_term);			//DAFTAR TERM LDA PADA KLUSTER TERPILIH

				$total = count($list_nrp);
				$nrp_testing = array();
				for ($x=0; $x <$total ; $x++) { 
					if($x%2==0){
						array_push($nrp_testing, $list_nrp[$x]);
					}
				}

				$mDoc = count($list_nrp);
				$nTerm = count($list_term);
				$matrix_m_n = array();
				$logP = array();
				for ($m=0; $m <$mDoc ; $m++) { 
					$doc = dbDokumen::find($list_nrp[$m]);

					$katakata = explode(' ', $doc->abstrak_af_preproc);
					$N = count($katakata);
					$list = array();
					for ($t=0; $t <$nTerm ; $t++) { 
						$list[$t] = 0;
					}

					for ($n=0; $n < $N; $n++) { 
						$idx = array_search(trim($katakata[$n]), $list_term);
						$list[$idx]++;	
					}
					$matrix_m_n[$m] = $list;
					$logP[$m] = 0;
				}

				
				for ($m=0; $m <$mDoc ; $m++) { 
					$nLog = 0;
					for ($n=0; $n <$nTerm ; $n++) { 
						$phiTheta = 0;
						for ($k=0; $k <$kTopik ; $k++) { 
							$phiTheta += ($phi_matrix[$n][$k]*$theta_matrix[$m][$k]);
						}
						$logPhiTheta = log($phiTheta);
						$nLog += ($matrix_m_n[$m][$n]*$logPhiTheta);
					}
					$logP[$m] = $nLog;
				}

				$num=0;
				foreach ($logP as $key => $value) {
					$num += $value;
				}

				$den = 0;
				for ($m=0; $m <$mDoc ; $m++) { 
					for ($n=0; $n <$nTerm ; $n++) { 
						$den += $matrix_m_n[$m][$n];
					}
				}

				$result = exp((-$num)/$den);
				return $result;
			// 	echo "PerplexityLDA ".$i." = ".$result."<br />";
			// }
		}
	}
?>