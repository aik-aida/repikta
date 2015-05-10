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