<?php

	class RepiktaController extends BaseController{

		public function show_phi(){
			$id_lda = 1;
			$data_lda = dbLdaSave::find($id_lda);
			$ktopik = $data_lda->k_topik;
			$nterm = $data_lda->n_term;
			$list_term = json_decode($data_lda->matriks_term);
			$phi = json_decode($data_lda->matriks_phi);
			echo "<br />";
			echo "Banyak Topik : ".$ktopik."<br />";
			echo "Banyak Kata : ".$nterm."<br />";
				
			//for ($k=0; $k <$ktopik ; $k++) { 
			for ($k=7; $k <8 ; $k++) { 
				echo "<br />";
				$hasil = array();
				$sum = 0.0;
				for ($n=0; $n <$nterm ; $n++) { 
					//echo $n.") ".$list_term[$n]." --- ".$phi[$n][$k]."<br />";
					$hasil[$list_term[$n]] = $phi[$n][$k];	
					$sum += $phi[$n][$k];
				}
				arsort($hasil);

				// $potong = $nterm-10;
				// $top10 = array_slice($hasil,0,10);
				// $i=1;
				// echo "TOPIK ".($k+1)."<br />";
				// echo "----------------------<br />";
				// foreach ($top10 as $key => $value) {
				// 	echo $i.") ".$key." --- ".$value."<br />"; $i++;
				// }
			}
			echo "jumlah = ".$sum."<br />";
			var_dump($hasil);
		}
	}

?>