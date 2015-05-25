<?php
	/**
	* 
	*/
	class Testing
	{
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

		public function FunctionName($value='')
		{
			# code...
		}
	}
?>