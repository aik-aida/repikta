<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('home');
});

Route::get('hai', function(){
	return View::make('tabpanel');
});

Route::post('read_transkrip', 'TranskripController@read');

Route::post('autentifikasi', 'BaseController@login');

Route::get('centroid', 'AdminController@centroid_list');

Route::post('centroid/detail', 'AdminController@centroid_detail');

Route::get('kamus', 'AdminController@kamus_list');

Route::get('dokumen', 'AdminController@dokumen_list');

Route::post('dokumen/detail', 'AdminController@dokumen_detail');

Route::post('dokumen/nilai_tf', 'AdminController@dokumen_tf');

Route::post('dokumen/nilai_tfidf', 'AdminController@dokumen_tfidf');

Route::get('kluster', 'AdminController@kluster_list');

Route::post('kluster/detail', 'AdminController@kluster_detail');

Route::get('lihatPHI', 'RepiktaController@show_phi');
Route::get('lihatTETA', 'RepiktaController@show_theta');

Route::get('dokumen_terdekat', function(){
	$repikta = new Repikta;
	$repikta->GetClosest();
});

Route::get('ekstrak_topik', function(){
	$counter = new TimeExecution;
	$awal = $counter->getTime();

	$masing2topik = array(5,3,4);
	$id_group = 1;
	$id_result = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
	$data_result = KmeansResult::find($id_result);
	$banyak_kluster = $data_result->jumlah_kluster;
	$hasil_kluster = json_decode($data_result->hasil_kluster);

	for ($i=0; $i <$banyak_kluster ; $i++) { 
	//for ($i=0; $i <1 ; $i++) { 
		//$k = $masing2topik[2];
		$k = $masing2topik[$i];
		$lda = new LdaGibbsSampling();
		$lda->TopicExtraction($k, $hasil_kluster[$i], $id_result, $i, $id_group);
		//$lda->TopicExtraction($k, $hasil_kluster[2], $id_result, 2, $id_group);
	}

	$akhir = $counter->getTime();
	$lama = ($akhir-$awal);
	echo "SUDAH BISA DILIHAT HASIL LDA-NYA : ".$id_group." , ".$id_result."<br />"." lama : ".$lama." detik <br />";

	echo "----------------------------------------------------THETA-----------------------------------------------<br />";
	$theta = $lda->theta;
	foreach ($theta as $key => $value) {
		foreach ($value as $key => $val) {
			echo $val." , ";
		}
		echo "<br />";
	}
	echo "<br /><br /><br />";
	echo "----------------------------------------------------PHIII-----------------------------------------------<br />";
	$phi = $lda->phi;
	foreach ($phi as $key => $value) {
		foreach ($value as $key => $val) {
			echo $val." , ";
		}
		echo "<br />";
	}
});

Route::get('clustering',function(){
	$counter = new TimeExecution;
	$startTime = $counter->getTime();
	
	$kmeans = new Kmeans;

	$doc_training = Dokumen::where('training','=',true)->get();
	$centroid_choose = 
	$number_k = 
	$k = 3;
	$n = count($doc_training);

	echo "n=".$n." - k=".$k."<br />"."<br />";
	//a:abstrak
	//ja:judul+abstrak
	//g:generated
	//m:manual
	$id_group = $kmeans->Clustering($k, $n, 'ja', 'm');
	
	for ($i=0; $i < count($kmeans->centroid); $i++) { 
		echo "Kluster ".($i+1)."<br />";
		if(count($kmeans->resultCluster[$i])>0){
			foreach ($kmeans->resultCluster[$i] as $key => $dokumen) {
				echo $dokumen->nrp."<br />";
			}
		}
		else{
			echo "Kosong <br />";
		}
		echo "<br />";
	}
	echo "iterasi ".$kmeans->counter."<br />";
	$endTime = $counter->getTime();
	echo ($endTime-$startTime)."detik <br />";

	$rk = new Repikta;
	$rk->Generate_Transkrip_Kriteria($id_group);

	//--Cluster Varian--

	$counter_CV = new TimeExecution;
	$startTime_CV = $counter_CV->getTime();
	$KedekatanKluster = new ClusterVariance;
	$id_group = 1;
	$id = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
	echo $id."<br />";
	$hasil = KmeansResult::find($id);
	echo $hasil->jumlah_kluster."<br />";
	$hasil_kluster = json_decode($hasil->hasil_kluster);

	$nilai_dekat = $KedekatanKluster->ClusterValue($hasil->jumlah_kluster, $hasil_kluster, $id, 'ja');
	$endTime_CV = $counter_CV->getTime();
	echo $nilai_dekat." - LAMA : ".($endTime_CV-$startTime_CV)." detik <br />";
});

Route::get('inisial', function(){
	$counter = new TimeExecution;
	$time1 = $counter->getTime();

	$prepoc = new Preprocessing;
	$prepoc->Reset_TfIdf();

	$time2 = $counter->getTime();
	$file_cent = $prepoc->ReadFile("./data/dt_centroid.txt");
	$dt_cent = $prepoc->ReadCentroid($file_cent);
	$dt_train = $prepoc->ReadFile("./data/dt_training.txt");
	$dt_test = $prepoc->ReadFile("./data/dt_testing.txt");
	$prepoc->Set_training_testing($dt_train, $dt_test);
	
	$time3 = $counter->getTime();
	$prepoc->PreprocessingText(); $time4 = $counter->getTime();
	$prepoc->DistinctTerm(); $time5 = $counter->getTime();
	$prepoc->CountIDF(); $time6 = $counter->getTime();
	$prepoc->CountTF(); $time7 = $counter->getTime();
	$prepoc->CountTF_IDF(); $time8 = $counter->getTime();
	$prepoc->PembobotanTF_IDF(); $time9 = $counter->getTime();

	$prepoc->Calculate_Save_Centroid($dt_cent);
	
	echo "preparing data : ".($time3-$time2)."detik <br />";	
	echo "Preprocessing text : ".($time4-$time3)."detik <br />";	
	echo "distinct term : ".($time5-$time4)."detik <br />";	
	echo "hitung IDF : ".($time6-$time5)."detik <br />";	
	echo "hitung TF : ".($time7-$time6)."detik <br />";	
	echo "hitung TF-IDF : ".($time8-$time7)."detik <br />";	
	echo "Pembobotan : ".($time9-$time8)."detik <br />";	
	echo "done";
});

Route::get('cek',function(){
	$string = "pemilu jaringan layanan jurusan berbasis kendaraan tujuan";
	//tokenizing dan stemming sastrawi
				$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
				$stemmer  = $stemmerFactory->createStemmer();
				$teks = $stemmer->stem($string);
				echo $string."<br />";
				echo $teks."<br />";
});
	
?>