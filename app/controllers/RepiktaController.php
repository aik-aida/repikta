<?php

	class RepiktaController extends BaseController{

		public function show_phi(){
			$id_lda = 26;
			$data_lda = dbLdaSave::find($id_lda);
			$ktopik = $data_lda->k_topik;
			$nterm = $data_lda->n_term;
			$list_term = json_decode($data_lda->matriks_term);
			$phi = json_decode($data_lda->matriks_phi);
			echo "<br />";
			echo "Banyak Topik : ".$ktopik."<br />";
			echo "Banyak Kata : ".$nterm."<br />";
			echo "Percobaan Ke : ".$data_lda->percobaan_ke."<br />";
				
			for ($k=0; $k <$ktopik ; $k++) { 
			//for ($k=7; $k <8 ; $k++) { 
				echo "<br />";
				$hasil = array();
				$sum = 0.0;
				for ($n=0; $n <$nterm ; $n++) { 
					//echo $n.") ".$list_term[$n]." --- ".$phi[$n][$k]."<br />";
					$hasil[$list_term[$n]] = $phi[$n][$k];	
					$sum += $phi[$n][$k];
				}
				arsort($hasil);
				echo "jumlah = ".$sum."<br />";
				//$potong = $nterm-20;
				$top10 = array_slice($hasil,0,20);
				$i=1;
				echo "TOPIK ".($k+1)."<br />";
				echo "----------------------<br />";
				foreach ($top10 as $key => $value) {
					//echo $i.") ".$key." --- ".$value."<br />"; $i++;
					echo $value."<br />"; $i++;
				}
			}
			
			// // var_dump($hasil);

			// foreach ($phi as $key => $value) {
			// 	foreach ($value as $key => $val) {
			// 		echo $val." , ";
			// 	}
			// 	echo "<br />";
			// }
		}

		public function show_theta(){
			$id_lda = 27;
			$data_lda = dbLdaSave::find($id_lda);
			$theta = json_decode($data_lda->matriks_theta);
			foreach ($theta as $key => $value) {
				foreach ($value as $key => $val) {
					echo $val." , ";
				}
				echo "<br />";
			}
		}
	}

?>