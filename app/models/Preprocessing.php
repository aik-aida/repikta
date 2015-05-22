<?php
	/**
	* 
	*/
	class Preprocessing
	{
		
		function __construct()
		{
			# code...
		}

		public function Reset_TfIdf(){
			$all = dbDokumen::all();
			foreach ($all as $key => $value) {
				$doc = dbDokumen::find($value->nrp);
				$doc->nilai_tf_abstrak = '';
				$doc->nilai_tfidf_abstrak = '';
				$doc->nilai_tf_judul = '';
				$doc->nilai_tfidf_judul = '';
				$doc->nilai_tfidf = '';
				$doc->save();
			}
		}

		public function InisialData(){

		}

		public function PembobotanTF_IDF(){
			$bobot_judul = 0.7;
			$bobot_abstrak = 0.3;

			$dokumens = dbDokumen::where('training','=',true)->get();
			$words = dbKamusKata::all();
			foreach ($dokumens as $key => $dokumen) {
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
			}
		}

		public function CountTF_IDF(){
			$dokumens = dbDokumen::where('training','=',true)->get();
			$words = dbKamusKata::all();
			$judul_words = dbKamusJudul::all();

			foreach ($dokumens as $key => $dokumen) {
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
				//echo $dokumen->nrp."<br />";
			}
		}

		public function CountTF(){
			$dokumens = dbDokumen::where('training','=',true)->get();
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
				//echo $dokumen->nrp."<br />";
			}
		}

		public function CountIDF(){
			$dokumens = dbDokumen::where('training','=',true)->get();

			$words = dbKamusKata::all();
			foreach ($words as $key => $kata) {
				//echo($kata->kata_dasar)."<br />";
				$count = 0;
				$count_doc = 0;
				$docs = array();
				foreach ($dokumens as $key => $dokumen) {
					$count = substr_count($dokumen->abstrak_af_preproc, ' '.$kata->kata_dasar.' ');
					if($count>0){
						$count_doc++;
						array_push($docs, $dokumen->nrp);
					}
				}
				if($count_doc>0){
					$idf = (float)(log10((float)count($dokumens)/(float)$count_doc));
					$iddoc = json_encode($docs);	

						$kamus = dbKamusKata::find($kata->kata_dasar);
						$kamus->idf = $idf;
						$kamus->indoc = $iddoc;
						$kamus->save();
				}
			}

			$judul_words = dbKamusJudul::all();
			foreach ($judul_words as $key => $kata) {
				//echo($kata->kata_dasar)."<br />";
				$count = 0;
				$count_doc = 0;
				$docs = array();
				foreach ($dokumens as $key => $dokumen) {
					$count = substr_count($dokumen->judul_af_preproc, ' '.$kata->kata_dasar.' ');
					if($count>0){
						$count_doc++;
						array_push($docs, $dokumen->nrp);
					}
				}
				if($count_doc>0){
					$idf = (float)(log10((float)count($dokumens)/(float)$count_doc));
					$iddoc = json_encode($docs);	

						$kamus = dbKamusJudul::find($kata->kata_dasar);
						$kamus->idf = $idf;
						$kamus->indoc = $iddoc;
						$kamus->save();
				}
			}
		}

		public function DistinctTerm(){
			$count = 0;
			$countJ = 0;
			$dokumen = dbDokumen::where('training','=',true)->get();

			$cDoc = count($dokumen);
			for ($i=0; $i < $cDoc	; $i++) { 
				//echo $i."-".$dokumen[$i]->nrp."<br />";
				$words = explode(' ', $dokumen[$i]->abstrak_af_preproc);
				$judul = explode(' ', $dokumen[$i]->judul_af_preproc);
				foreach ($words as $key => $kata) {
					if(dbKamusKata::find($kata)==null && $kata!=' ' && strlen($kata)!=0){
						//array_push($new, $kata);
						$check = new dbKamusKata;
						$check->kata_dasar = $kata;
						$check->save();
						$count++;
					}
				}
				foreach ($judul as $key => $kata) {
					if(dbKamusKata::find($kata)==null && $kata!=' ' && strlen($kata)!=0){
						//array_push($new, $kata);
						$check = new dbKamusKata;
						$check->kata_dasar = $kata;
						$check->save();
						$count++;
					}

					if(dbKamusJudul::find($kata)==null && $kata!=' ' && strlen($kata)!=0){
						//array_push($new, $kata);
						$check = new dbKamusJudul;
						$check->kata_dasar = $kata;
						$check->save();
						$countJ++;
					}
				}
			}
		}

		public function PreprocessingText(){
			//mengambil seluruh data dokumen
			$dokumens = dbDokumen::all();

			//pra proses untuk setiap dokumen
			foreach ($dokumens as $key => $value) {
				$teks = (object) array("input","afstemming","afremoval","output");
				$judul = (object) array("input","afstemming","afremoval","output");

				$teks->input = $value->abstraksi_ta;
				$judul->input = $value->judul_ta;

				//tokenizing dan stemming sastrawi
				$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
				$stemmer  = $stemmerFactory->createStemmer();
				$teks->afstemming = $stemmer->stem($teks->input);
				$judul->afstemming = $stemmer->stem($judul->input);

				//stopword removal sastrawi
				$stopwordRemoval= new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
				$removal  = $stopwordRemoval->createStopWordRemover();
				$teks->afremoval = $removal->remove($teks->afstemming);
				$judul->afremoval = $removal->remove($judul->afstemming);

				//proses tambahan penghilangan teks angka
				$teks->output = $this->AdditionalPreproc($teks->afremoval);
				$judul->output = $this->AdditionalPreproc($judul->afremoval);

				//menyimpan hasil pra proses dokumen
				$update_preprocessing = dbDokumen::find($value->nrp);
				$update_preprocessing->abstrak_af_preproc = " ".$teks->output." ";
				$update_preprocessing->judul_af_preproc = " ".$judul->output." ";
				$update_preprocessing->save();
			}
		}

		public function AdditionalPreproc($teks){
			$content = explode(" ", $teks);
			$delword = array();
			foreach ($content as $key => $val) {
				$get_number = preg_replace("/[^0-9]/","",$val);
				if(is_numeric($get_number))
				{
					array_push($delword, $val);
				}
			}
			array_push($delword, '0');
			array_push($delword, '1');
			array_push($delword, '2');
			array_push($delword, '3');
			array_push($delword, '4');
			array_push($delword, '5');
			array_push($delword, '6');
			array_push($delword, '7');
			array_push($delword, '8');
			array_push($delword, '9');
			return str_replace($delword, '', $teks);
		}

		public function Calculate_Save_Centroid($arrNRP){
			$kamus = dbKamusKata::all();
				$k_number = count($arrNRP);
				echo "k:".$k_number."<br />";
				$newCentroid = array();
				for ($i=0; $i < $k_number; $i++) { 
						$newCentroid[$i] = (object) array();
						$n = count($arrNRP[$i]);
						echo ($i+1)."-".$n."<br />";
						foreach ($kamus as $key => $kata) {
							$term = $kata->kata_dasar;
							$sum = 0.0;
							foreach ($arrNRP[$i] as $key => $nrp) {
								$dokumen = dbDokumen::find($nrp);
								$vectorDoc = json_decode($dokumen->nilai_tfidf); //untuk pake hasil pembobotan
								$sum += $vectorDoc->$term;
							}
							$avg = $sum/$n;
							$newCentroid[$i]->$term = $avg;
						}
				}

			$all = dbCentroidManual::all();
			$cek = 0;
			foreach ($all as $key => $value) {
				if( $value->teks=='ja' && $value->k==$k_number && $value->dokumen_centroid==json_encode($arrNRP) && $value->centroid==json_encode($newCentroid) ){
					$cek = 1;
					echo "SUDAH KESIMPAN SEBELUMNYA, centroid no : ".$value->id."<br />";
				}
			}

			if($cek==0) {
				echo "CENTROID MANUAL BARU <br />";
				$simpan = new dbCentroidManual;
				$simpan->teks = 'ja';
				$simpan->k = $k_number;
				$simpan->dokumen_centroid = json_encode($arrNRP);
				$simpan->centroid = json_encode($newCentroid);
				$simpan->save();
			}
		}

		public function ReadFile($pathFile){
			$file = fopen($pathFile,"r");
			$arrNRP = array();
			while(! feof($file))
			{
				array_push($arrNRP, trim(fgets($file)));
			}

			fclose($file);
			return $arrNRP;
		}

		public function ReadCentroid($arr){
			$id=0;
			$centroid = array();
			foreach ($arr as $key => $value) {
				//echo $value."<br />";
				if($id==1 && substr($value, 0, 2)=="51"){
					array_push($centroid[0], $value);
				}
				
				if ($id==2 && substr($value, 0, 2)=="51") {
					array_push($centroid[1], $value);
				}
				
				if ($id==3 && substr($value, 0, 2)=="51") {
					array_push($centroid[2], $value);
				}

				if($value==1){
					$id=1;
					$centroid[0]=array();
					//echo "1<br />";
				}
				
				if ($value==2) {
					$id=2;
					$centroid[1]=array();
					//echo "2<br />";
				}
				
				if ($value==3) {
					$id=3;
					$centroid[2]=array();
					//echo "3<br />";
				}
				
			}

			//var_dump($centroid);
			return $centroid;
		}

		public function Set_training_testing($train, $test){
			foreach ($train as $key => $value) {
				$doc = dbDokumen::find($value);
				$doc->training = true;
				$doc->save();
			}
			
			foreach ($test as $key => $value) {
				$doc = dbDokumen::find($value);
				$doc->training = false;
				$doc->save();
			}

		}
	}
?>