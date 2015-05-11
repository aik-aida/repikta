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
			$all = Dokumen::all();
			foreach ($all as $key => $value) {
				$doc = Dokumen::find($value->nrp);
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

		public function DistinctTerm(){
			$count = 0;
			$countJ = 0;
			$dokumen = Dokumen::where('training','=',true)->get();

			$cDoc = count($dokumen);
			for ($i=0; $i < $cDoc	; $i++) { 
				//echo $i."-".$dokumen[$i]->nrp."<br />";
				$words = explode(' ', $dokumen[$i]->abstrak_af_preproc);
				$judul = explode(' ', $dokumen[$i]->judul_af_preproc);
				foreach ($words as $key => $kata) {
					if(KamusKata::find($kata)==null && $kata!=' ' && strlen($kata)!=0){
						//array_push($new, $kata);
						$check = new KamusKata;
						$check->kata_dasar = $kata;
						$check->save();
						$count++;
					}
				}
				foreach ($judul as $key => $kata) {
					if(KamusKata::find($kata)==null && $kata!=' ' && strlen($kata)!=0){
						//array_push($new, $kata);
						$check = new KamusKata;
						$check->kata_dasar = $kata;
						$check->save();
						$count++;
					}

					if(KamusJudul::find($kata)==null && $kata!=' ' && strlen($kata)!=0){
						//array_push($new, $kata);
						$check = new KamusJudul;
						$check->kata_dasar = $kata;
						$check->save();
						$countJ++;
					}
				}
			}
		}

		public function PreprocessingText(){
			//mengambil seluruh data dokumen
			$dokumens = Dokumen::all();

			//pra proses untuk setiap dokumen
			foreach ($dokumens as $key => $value) {
				$teks = (object) array("input","afstemming","afremoval","output");
				$judul = (object) array("input","afstemming","afremoval","output");

				$teks->input = $value->abstraksi_ta;
				$teks->input = $value->judul_ta;

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
				$update_preprocessing = Dokumen::find($value->nrp);
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

		public function ReadFile($pathFile){
			$file = fopen($pathFile,"r");
			$arrNRP = array();
			while(! feof($file))
			{
				array_push($arrNRP, fgets($file));
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
				$doc = Dokumen::find(trim($value));
				$doc->training = true;
				$doc->save();
			}
			
			foreach ($test as $key => $value) {
				$doc = Dokumen::find(trim($value));
				$doc->training = false;
				$doc->save();
			}

		}
	}
?>