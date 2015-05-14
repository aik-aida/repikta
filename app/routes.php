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

Route::get('terdekat', function(){
	$id_group = 1;
	$dokumen_testing = Dokumen::where('training','=',false)->get();
	$id_result = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
	$data_kluster = KmeansResult::find($id_result);
	$hasil_kluster = json_decode($data_kluster->hasil_kluster);
	$repikta = new Repikta;
	for ($i=0; $i <count($dokumen_testing) ; $i++) { 
		$counter = new TimeExecution;
		$startTime = $counter->getTime();
		$nrp = $dokumen_testing[$i]->nrp;
		echo $nrp."<br />";
		$idk = $repikta->Choose_Kluster($nrp, $id_group);
		echo "dekat ".$idk."<br /> ";

		$distance = array();
		foreach ($hasil_kluster[$idk] as $key => $nrppembanding) {
			$counter_t = new TimeExecution;
			$startTime_t = $counter_t->getTime();
			$pembanding_doc = Dokumen::find($nrppembanding);
			$nrp_tr = json_decode($dokumen_testing[$i]->transkrip);
			$pembanding_tr = json_decode($pembanding_doc->transkrip);
			$dist = $repikta->EuclideanTranskrip($nrp_tr, $pembanding_tr);
			array_push($distance, $dist);
			//echo $nrppembanding."-".$dist."<br />";
			$endTime_t = $counter_t->getTime();
			$simpan = new dbTranskripDistanceKluster;
			$simpan->group = $id_group;
			$simpan->id_kluster = $id_result;
			$simpan->index = $idk;
			$simpan->nrp = $nrp;
			$simpan->pembanding = $nrppembanding;
			$simpan->distance = $dist;
			$simpan->lama = ($endTime_t-$startTime_t);
			$simpan->save();
		}

		$index = array_search(min($distance), $distance);
		echo "TERDEKAT ".$hasil_kluster[$idk][$index]."<br />";
		$endTime = $counter->getTime();
		echo ($endTime-$startTime)." detik <br />";
		echo "<br />";
	}
});

Route::get('all_distance',function(){
	$dokumen_testing = Dokumen::where('training','=',false)->get();
	$dokumen_training = Dokumen::where('training','=',true)->get();
	$repikta = new Repikta;
	$distance = array();
	for ($i=0; $i <count($dokumen_testing) ; $i++) { 
		$counter = new TimeExecution;
		$startTime = $counter->getTime();
		$nrp = $dokumen_testing[$i]->nrp;
		echo $nrp."<br />";

		$distance = array();
		foreach ($dokumen_training as $key => $docpembanding) {
			if($nrp!=$docpembanding->nrp){
				$counter_t = new TimeExecution;
				$startTime_t = $counter_t->getTime();
				$nrp_tr = json_decode($dokumen_testing[$i]->transkrip);
				$pembanding_tr = json_decode($docpembanding->transkrip);
				$dist = $repikta->EuclideanTranskrip($nrp_tr, $pembanding_tr);
				array_push($distance, $dist);
				//echo $docpembanding->nrp."-".$dist."<br />";
				$endTime_t = $counter_t->getTime();
				$simpan = new dbTranskripDistanceAll;
				$simpan->nrp = $nrp; 
				$simpan->pembanding = $docpembanding->nrp;
				$simpan->distance = $dist;
				$simpan->lama = ($endTime_t-$startTime_t);
				$simpan->save();
			}
		}

		$index = array_search(min($distance), $distance);
		echo "TERDEKAT ".$dokumen_training[$index]->nrp."<br />";
		$endTime = $counter->getTime();
		echo ($endTime-$startTime)." detik <br />";
		echo "<br />";

		
	}
});

Route::get('ekstrak_topik', function(){
	$counter = new TimeExecution;
	$awal = $counter->getTime();

	$masing2topik = array(8,4,4);
	$id_group = 1;
	$id_result = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
	$data_result = KmeansResult::find($id_result);
	$banyak_kluster = $data_result->jumlah_kluster;
	$hasil_kluster = json_decode($data_result->hasil_kluster);

	for ($i=0; $i <$banyak_kluster ; $i++) { 
	// for ($i=0; $i <1 ; $i++) { 
		$k = $masing2topik[$i];
		$lda = new LdaGibbsSampling();
		$lda->TopicExtraction($k, $hasil_kluster[$i], $id_result, $i, $id_group);
	}

	$akhir = $counter->getTime();
	$lama = ($akhir-$awal);

	echo "SUDAH BISA DILIHAT HASIL LDA-NYA : ".$id_group." , ".$id_result."<br />"." lama : ".$lama." detik <br />";
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

Route::get('gettranskrip', function(){
	$data = dbTranskripEkivalensi::where('nrp', '=', '5110100018')->get();
	$mk = dbMataKuliah::all();
	$no=0;
	// foreach ($data as $key => $value) {
	// 	$no++;
	// 	echo "[".$no."] ".$value->mk_id." : ".$value->nilai_huruf."-".$value->nilai_angka."<br />";
	// }

	//$dataNRP = dbTranskripEkivalensi::select('nrp')->distinct()->get();
	$dataNRP = dbDokumen2009::select('nrp')->where('transkrip','=',null)->distinct()->get();
	$total = 0;
	foreach ($dataNRP as $key => $value) {
		//echo $value->nrp."<br />";
		$nrp = $value->nrp;
		$simpan = dbDokumen2009::find($nrp);
		// $simpan = Dokumen::find($nrp);
		// if(count($simpan)==0){
		// 	$simpan = dbDokumen2009::find($nrp);
		// }
		echo "NRP : ".$nrp." - ";
		$totalnilai = 0;
		
		$json_transkrip = (object) array();
		foreach ($mk as $key => $mkdata) {
			$nilai = dbTranskrip110all::where('kn_ku_ma_nrp', '=', $nrp)->where('kn_ku_ke_kr_mk_id', '=', $mkdata->mk_kode)->get();
			$kode = $mkdata->mk_kode;
			if(count($nilai)>0){
				$no++;
				$id=0;
				if(count($nilai)>1){
					$th=0;
					$sm=0;
					for ($i=0; $i < count($nilai) ; $i++) { 
						if($nilai[$i]->kn_ku_ke_tahun >= $th){
							if($nilai[$i]->kn_ku_ke_idsemester >= $sm){
								$th = $nilai[$i]->kn_ku_ke_tahun;
								$sm = $nilai[$i]->kn_ku_ke_idsemester;
								$id = $i;
							}
						}
					}
					//echo "[".$no."] ".$nilai[$id]->mk_id." : ".$nilai[$id]->nilai_huruf."-".$nilai[$id]->nilai_angka."<br />";
					$json_transkrip->$kode = $nilai[$id]->kn_ku_nilaiAngka;
					
					//$hapus += (count($nilai)-1);
				}
				elseif (count($nilai)==1) {
					//echo "[".$no."] ".$nilai[0]->mk_id." : ".$nilai[0]->nilai_huruf."-".$nilai[0]->nilai_angka."<br />";

					$json_transkrip->$kode = $nilai[0]->kn_ku_nilaiAngka;
					$id=0;
				}
					$tambah = new dbTranskrip;
					// $tambah->tahun = $nilai[$id]->tahun;
					// $tambah->semester = $nilai[$id]->semester;
					// $tambah->nrp = $nilai[$id]->nrp;
					// $tambah->mk_tahun = $nilai[$id]->mk_tahun;
					// $tambah->mk_id = $nilai[$id]->mk_id;
					// $tambah->nilai_angka = $nilai[$id]->nilai_angka;
					// $tambah->nilai_huruf = $nilai[$id]->nilai_huruf;
					// $tambah->nilai_1 = $nilai[$id]->nilai_1;
					// $tambah->nilai_2 = $nilai[$id]->nilai_2;
					// $tambah->nilai_3 = $nilai[$id]->nilai_3;
					// $tambah->nilai_4 = $nilai[$id]->nilai_4;

					$tambah->tahun = $nilai[$id]->kn_ku_ke_tahun;
					$tambah->semester = $nilai[$id]->kn_ku_ke_idsemester;
					$tambah->nrp = $nilai[$id]->kn_ku_ma_nrp;
					$tambah->mk_tahun = $nilai[$id]->kn_ku_ke_kr_mk_ThnKurikulum;
					$tambah->mk_id = $nilai[$id]->kn_ku_ke_kr_mk_id;
					$tambah->nilai_angka = $nilai[$id]->kn_ku_nilaiAngka;
					$tambah->nilai_huruf = $nilai[$id]->kn_ku_nilaiHuruf;
					$tambah->nilai_1 = $nilai[$id]->kn_ku_n1;
					$tambah->nilai_2 = $nilai[$id]->kn_ku_n2;
					$tambah->nilai_3 = $nilai[$id]->kn_ku_n3;
					$tambah->nilai_4 = $nilai[$id]->kn_ku_n4;
					$tambah->save();

					$totalnilai++;
					$total++;
					//echo "111<br />";
			}
			else{
				//echo "---".$mkdata->mk_kode."---<br />";
				$json_transkrip->$kode = 0.0;
			}
		}

		//echo "xxx ".$simpan->nama."<br />";
		// var_dump($json_transkrip);
		// echo count($nilai);
		echo $totalnilai."<br />";
		$simpan->transkrip = json_encode($json_transkrip);
		$simpan->save();
	}
	echo "masuk ".$total." data";
});

Route::get('data', function()
{
	//gabungin data ta akademik
	/*
	$source = Abstrak110p::all();

	//var_dump($source);
	//$user = Abstrak110p::find('5110100018');
	$count_save=0;
	$count_updt=0;

	foreach ($source as $key => $value) {
		$now = Dokumen::find($value->MW_MA_NRP);
		if($now == null){
			$dokumen = new Dokumen;	
			$dokumen->nrp = $value->MW_MA_NRP ;
			$dokumen->nama = $value->MW_MA_NAMA ;
			$dokumen->email = $value->MW_MA_EMAIL ;
			//$dokumen->tahun = "20".substr($value->MW_MA_NRP,2,2) ;
			$dokumen->judul_ta = $value->MW_MA_JUDULTA.$value->MW_MA_JUDULTA2 ;
			$dokumen->abstraksi_ta = $value->MW_MA_ABSTRAK_ID ;
			if($value->MW_MA_PEMBIMBING1 != null)
				$dokumen->pembimbing1 = $value->MW_MA_PEMBIMBING1 ;
			if($value->MW_MA_PEMBIMBING2 != null)
				$dokumen->pembimbing2 = $value->MW_MA_PEMBIMBING2 ;
			
			//var_dump($dokumen);
			//echo "<br />";

			$dokumen->save();
			$count_save++;
			echo "save_data_".$count_save."<br />";
		}
		else{
			$now->nrp = $value->MW_MA_NRP ;
			$now->nama = $value->MW_MA_NAMA ;
			$now->email = $value->MW_MA_EMAIL ;
			//$now->tahun = "20".substr($value->MW_MA_NRP,2,2) ;
			$now->judul_ta = $value->MW_MA_JUDULTA.$value->MW_MA_JUDULTA2 ;
			$now->abstraksi_ta = $value->MW_MA_ABSTRAK_ID ;
			if($value->MW_MA_PEMBIMBING1 != null)
				$now->pembimbing1 = $value->MW_MA_PEMBIMBING1 ;
			if($value->MW_MA_PEMBIMBING2 != null)
				$now->pembimbing2 = $value->MW_MA_PEMBIMBING2 ;
			$now->save();
			$count_updt++;
			echo "update_data_".$count_updt."<br />";
		}
	}*/


});

Route::get('minmax', function(){
	$kamus = KamusKata::all();
	$corpus = Dokumen::all();
	$timer = new TimeExecution;
	echo "string";
	//$term = 'al-quran';
	$count = 0;
	$A = $timer->getTime();
	foreach ($kamus as $key => $kata) {
		$term = $kata->kata_dasar;
		$minmax = array();

		//untuk clustering berdasar abstrak saja
		foreach ($corpus as $key => $doc) {
			$vector = json_decode($doc->nilai_tfidf);
			array_push($minmax , $vector->$term);
		}

		// //untuk clustering berdasar abstrak saja
		// foreach ($corpus as $key => $doc) {
		// 	$vector = json_decode($doc->nilai_tfidf_abstrak);
		// 	array_push($minmax , $vector->$term);
		// }

		echo($count++); echo " - "; echo($term); echo "<br />";
		$updateMinMax = KamusKata::find($term);
		$updateMinMax->min_value = min($minmax);
		$updateMinMax->max_value = max($minmax);
		$updateMinMax->save();
	}
	$Z = $timer->getTime();
	echo "sudaaah, yeay! ".($Z-$A)." detik";
});

Route::get('testing', function(){
	$data1 = array(1,1);
	$data2 = array(2,1);
	$data3 = array(4,3);
	$data4 = array(5,4);
	$data5 = array(2,6);
	$data6 = array(3,7);
	$data7 = array(7,0);
	$data8 = array(1,2);
	$data9 = array(7,1);
	$data10 = array(2,7);
	$data = array($data1, $data2, $data3, $data4, $data5, $data6, $data7, $data8, $data9, $data10);

	$centroid = array($data1, $data8, $data10, $data6, $data4, $data9); //6
	//$centroid = array($data1, $data10, $data3, $data4, $data7); //5
	//$centroid = array($data1, $data5, $data3, $data7); //4
	//$centroid = array($data10, $data1, $data7); //3
	//$centroid = array($data1, $data7); //2

	$test = new KmeansManualData;
	//$test->Clustering(4, 10, $data, 2, $centroid);
	$test->Clustering(6, 10, $data, 2, $centroid);
	//echo $test->counter."<br />";
	var_dump($test->resultCluster);
});

Route::get('cosine', function(){

	function dotproduct($vector1, $vector2){
		$kamus = KamusKata::all();
		$dotprod = 0.0;

		// $v = array('satu', 'dua', 'tiga');
		// foreach ($v as $key => $value) {
		// 	$dotprod += (float)(($vector1->$value)*($vector2->$value));
		// }

		foreach ($kamus as $key => $kata) {
			$term = $kata->kata_dasar;
			$dotprod += (($vector1->$term)*($vector2->$term));
		}
		return (float) $dotprod;
	}	

	function magnitude($vector){
		return (float) sqrt(dotproduct($vector, $vector));
	}

	
	// $v1 = (object) array('satu'=>'1', 'dua'=>'2', 'tiga'=>'3');
	// $v2 = (object) array('satu'=>'3', 'dua'=>'2', 'tiga'=>'1');

	// $dot = dotproduct($v1, $v2);
	// $mg1 = magnitude($v1);
	// $mg2 = magnitude($v2);
	// echo ($dot/($mg1*$mg2));


});

//check for equal result kmeans klustering
Route::get('check', function(){
	// $a = array();
	// $a[0] = array(1,2,3);
	// $a[1] = array(1,1,1);
	// $a[2] = array(3,2,1);

	// $b = array();
	// $b[0] = array(1,2,3);
	// $b[1] = array(1,1,1);
	// $b[2] = array(3,2,1);

	// if($a == $b) echo "true";
	// else echo "false";

	//cek hasil kluster 3 6 8

	//id kluster 3 prev : 68
	//id kluster 3 next : 85
	//id kluster 6 prev : 71
	//id kluster 6 next : 88
	//id kluster 8 prev : 30
	//id kluster 8 next : 94
	$satu3 = KmeansResult::find(30);
	$duaa3 = KmeansResult::find(94);
	echo $satu3->jumlah_kluster."-".$duaa3->jumlah_kluster."<br />";
	$hasil_satu3 = json_decode($satu3->hasil_kluster);
	$hasil_duaa3 = json_decode($duaa3->hasil_kluster);
	if ($hasil_satu3==$hasil_duaa3) {
		echo "sama alhamdulillah";
	}
	else{
		echo "astaghfirullah";
	}
	echo "<br />";
	for ($j=0; $j < count($hasil_satu3) ; $j++) { 
		for ($i=0; $i < count($hasil_satu3[$j]) ; $i++) { 
			echo "[".$j.",".$i."] ";
			if($hasil_satu3[$j][$i]==$hasil_duaa3[$j][$i]) echo "sama";
			else echo "NO!!";
			echo "<br />";
		}
	}
});


Route::get('transkrip', function(){
	//--databaru--
	// $data = dbDokumen2009::select('nrp')->where('transkrip','=',null)->get();
	// $i=0;
	// foreach ($data as $key => $value) {
	// 	//echo $value->nrp."<br />";
	// 	//$i++;
	// }
	// //echo "jumlah ".$i;

	// $tr = dbTranskrip110::all();
	// foreach ($data as $key => $value) {
	// 	//$get = dbTranskrip110::find($value->nrp);
	// 	// $get = dbTranskrip110all::find($value->nrp);
	// 	// if(count($get)>0){
	// 	// 	echo $value->nrp."<br />";
	// 	// 	$i++;
	// 	// }

	// 	$db = DB::table('trans_jurusansw110_')->get();
	// 	$ii=0;
	// 	echo "--- ".$value->nrp." ---<br />";
	// 	foreach ($db as $key => $data) {
	// 		if($data->kn_ku_ma_nrp == $value->nrp){
	// 			echo "********** ".$data->kn_ku_ke_kr_mk_ThnKurikulum." - ".$data->kn_ku_ke_kr_mk_id." - ".$data->kn_ku_nilaiAngka."<br />";
	// 			$ii++;
	// 		}
	// 	}
	// 	echo "banyak nilai : ".$ii."<br />";
	// }
	//echo "jumlah ".$i;

	$data = dbDokumen2009::select('nrp')->where('transkrip','=',null)->distinct()->get();
	foreach ($data as $key => $value) {
		echo $value->nrp."<br />";
	}

	




	//EKUIVALENSI
	// $dataEQ = dbTranskripEkivalensi::where('mk_tahun', '=', '2014')->get();

	// //melihat mk 2014 yang butuh di ekuivalensiin.
	// // $mk2014 = dbTranskripEkivalensi::select('mk_tahun','mk_id')->where('mk_tahun','=','2014')->distinct()->get();
	// // foreach ($mk2014 as $key => $value) {
	// // 	echo $value->mk_tahun."-".$value->mk_id."<br />";
	// // }
	

	// foreach ($dataEQ as $key => $value) {
	// 	echo $value->nrp." : ".$value->mk_tahun.",".$value->mk_id." == ";
	// 	$new_id='';
	// 	switch ($value->mk_id) {
	// 		case 'KI1502':
	// 			$new_id='KI1391';
	// 			break;
	// 		case 'KI1431':
	// 			$new_id='KI1338';
	// 			break;
	// 		case 'KI1330':
	// 			$new_id='KI1392';
	// 			break;
	// 		case 'KI1410':
	// 			$new_id='KI1336';
	// 			break;
	// 		case 'KI1418':
	// 			$new_id='KI1355';
	// 			break;
	// 		case 'KI1437':
	// 			$new_id='KI1339';
	// 			break;
	// 		case 'KI1428':
	// 			$new_id='KI1341';
	// 			break;
	// 		case 'KI1425':
	// 			$new_id='KI1379';
	// 			break;
	// 		case 'KI1417':
	// 			$new_id='KI1375';
	// 			break;
	// 		case 'KI1426':
	// 			$new_id='KI1357';
	// 			break;
	// 		case 'KI1419':
	// 			$new_id='KI1358';
	// 			break;
	// 		case 'KI1427':
	// 			$new_id='KI1380';
	// 			break;
	// 	}
	// 	$upd = dbTranskripEkivalensi::find($value->id);
	// 	$upd->mk_tahun = '2009';
	// 	$upd->mk_id = $new_id;
	// 	$upd->save();

	// 	$check = dbTranskripEkivalensi::find($value->id);
	// 	echo $check->mk_tahun.",".$check->mk_id."<br />";
	// }
	// echo "coba lihaaaat !!!";


	//menyaring transkrip untuk pasangan abstrak.
	// $dataNRP = array();
	// $dokumens = Dokumen::all();
	// $dokumen2009s = array();
	// $dokumen2009all = dbDokumen2009::all();

	// foreach ($dokumens as $key => $value) {
	// 	array_push($dataNRP, $value->nrp);
	// }
	// foreach ($dokumen2009all as $key => $value) {
	// 	if($value->judul_ta!=null){
	// 		array_push($dataNRP, $value->nrp);	
	// 	}
	// }
	
	// $nrp110 = dbTranskrip110::select('kn_ku_ma_nrp')->distinct()->get();
	// $nrp111 = dbTranskrip111::select('kn_ku_ma_nrp')->distinct()->get();
	// $nrpTranskrip = array();
	// $berapa=0;
	// foreach ($nrp110 as $key => $value) {
	// 	$berapa++;
	// 	echo $berapa."<br />";
	// 	if(!in_array($value->kn_ku_ma_nrp, $dataNRP)){
	// 		//dbTranskrip110::where('kn_ku_ma_nrp', '=', $value->kn_ku_ma_nrp)->delete();
	// 		//echo $value->kn_ku_ma_nrp."<br />";
	// 		echo "eo gak ada<br />";
	// 	}
	// 	else{
	// 		array_push($nrpTranskrip, $value->kn_ku_ma_nrp);
	// 	}
	// }
	// foreach ($nrp111 as $key => $value) {
	// 	$berapa++;
	// 	echo $berapa."<br />";
	// 	if(!in_array($value->kn_ku_ma_nrp, $dataNRP)){
	// 		//dbTranskrip111::where('kn_ku_ma_nrp', '=', $value->kn_ku_ma_nrp)->delete();
	// 		//echo $value->kn_ku_ma_nrp."<br />";
	// 		echo "eo gak ada<br />";
	// 	}
	// 	else{
	// 		array_push($nrpTranskrip, $value->kn_ku_ma_nrp);
	// 	}
	// }
	// echo "selesai";
	// echo "hasilnya harus sama : <br />";
	// // if($dataNRP==$nrpTranskrip)
	// echo 'abstrak '.count($dataNRP)."<br />";
	// echo 'transkrip '.count($nrpTranskrip)."<br />";
	// // 	echo "SAMA";
	// // else
	// // 	echo "astaghfirullah";
	// $no=0;
	// foreach ($dataNRP as $key => $value) {
	// 	$no++;
	// 	echo $no.". ";
	// 	if(in_array($value, $nrpTranskrip)){
	// 		echo "ada<br />";
	// 	}
	// 	else{
	// 		echo "---NO---<br />";	
	// 	}
	// }

	//-----

	//melihat mk 2014 yang butuh di ekuivalensiin.
	// $nrp110 = dbTranskrip110::select('kn_ku_ke_kr_mk_ThnKurikulum','kn_ku_ke_kr_mk_id')->distinct()->get();
	// $nrp111 = dbTranskrip111::select('kn_ku_ke_kr_mk_ThnKurikulum','kn_ku_ke_kr_mk_id')->distinct()->get();
	// $mk2014 = dbTranskrip111::select('kn_ku_ke_kr_mk_ThnKurikulum','kn_ku_ke_kr_mk_id')->where('kn_ku_ke_kr_mk_ThnKurikulum','=','2014')->distinct()->get();
	// // foreach ($mk2014 as $key => $value) {
	// // 	echo $value->kn_ku_ke_kr_mk_ThnKurikulum."-".$value->kn_ku_ke_kr_mk_id."<br />";
	// // }
	// $notyet = array('KI1422', 'KI1423', 'KI1430', 'KI1420', 'KI1429', 'KI1434', 'KI1325', 'KI1501');
	// $nrp = array();
	// foreach ($notyet as $key => $value) {
	// 	$daftar = dbTranskrip111::where('kn_ku_ke_kr_mk_ThnKurikulum','=','2014')->where('kn_ku_ke_kr_mk_id','=',$value)->get();
	// 	if($daftar!=null){
	// 		foreach ($daftar as $key => $data) {
	// 			// if(in_array($data->kn_ku_ma_nrp, $nrp)==null){
	// 			// 	array_push($nrp, $data->kn_ku_ma_nrp);
	// 			// }
	// 			array_push($nrp, $data->kn_ku_ma_nrp."-".$data->kn_ku_ke_kr_mk_id);
	// 		}
	// 	}
	// }
	// foreach ($nrp as $key => $value) {
	// 	echo $value."<br />";
	// }

	//hapus mk yang tidak ada dalam ekuivalensi + pra TA
	// $simpan = new dbTranskripEkivalensi;
	// 	$simpan->tahun = ;
	// 	$simpan->semester = ;
	// 	$simpan->nrp = ;
	// 	$simpan->mk_tahun = ;
	// 	$simpan->mk_id = ;
	// 	$simpan->nilai_angka = ;
	// 	$simpan->nilai_huruf = ;
	// 	$simpan->nilai_1 = ;
	// 	$simpan->nilai_2 = ;
	// 	$simpan->nilai_3 = ;
	// 	$simpan->nilai_4 = ;

	// $mkhapus = array('KI1422', 'KI1423', 'KI1430', 'KI1420', 'KI1429', 'KI1434', 'KI1325', 'KI1501');
	// $data1 = dbTranskrip110::all();
	// foreach ($data1 as $key => $value) {
	// 	$simpan = new dbTranskripEkivalensi;
	// 	$simpan->tahun = $value->kn_ku_ke_tahun;
	// 	$simpan->semester = $value->kn_ku_ke_idsemester;
	// 	$simpan->nrp = $value->kn_ku_ma_nrp;
	// 	$simpan->mk_tahun = $value->kn_ku_ke_kr_mk_ThnKurikulum;
	// 	$simpan->mk_id = $value->kn_ku_ke_kr_mk_id;
	// 	$simpan->nilai_angka = $value->kn_ku_nilaiAngka;
	// 	$simpan->nilai_huruf = $value->kn_ku_nilaiHuruf;
	// 	$simpan->nilai_1 = $value->kn_ku_n1;
	// 	$simpan->nilai_2 = $value->kn_ku_n2;
	// 	$simpan->nilai_3 = $value->kn_ku_n3;
	// 	$simpan->nilai_4 = $value->kn_ku_n4;
	// 	$simpan->save();
	// }
	// echo "check";

	// $data2 = dbTranskrip111::all();
	// $hapus=0;
	// foreach ($data2 as $key => $value) {
	// 	if(in_array($value->kn_ku_ke_kr_mk_id, $mkhapus) && $value->kn_ku_ke_kr_mk_ThnKurikulum=='2014'){
	// 		$hapus++;
	// 		echo $hapus." ".$value->kn_ku_ma_nrp."-".$value->kn_ku_ke_kr_mk_id."<br />";
	// 	}else{
	// 		$simpan = new dbTranskripEkivalensi;
	// 		$simpan->tahun = $value->kn_ku_ke_tahun;
	// 		$simpan->semester = $value->kn_ku_ke_idsemester;
	// 		$simpan->nrp = $value->kn_ku_ma_nrp;
	// 		$simpan->mk_tahun = $value->kn_ku_ke_kr_mk_ThnKurikulum;
	// 		$simpan->mk_id = $value->kn_ku_ke_kr_mk_id;
	// 		$simpan->nilai_angka = $value->kn_ku_nilaiAngka;
	// 		$simpan->nilai_huruf = $value->kn_ku_nilaiHuruf;
	// 		$simpan->nilai_1 = $value->kn_ku_n1;
	// 		$simpan->nilai_2 = $value->kn_ku_n2;
	// 		$simpan->nilai_3 = $value->kn_ku_n3;
	// 		$simpan->nilai_4 = $value->kn_ku_n4;
	// 		$simpan->save();	
	// 	}
	// }
	// echo "8248+2629 = 10877";
});

Route::get('datadata',function(){
	//masukkan matakuliah
	// $mk = dbMK::all();
	// foreach ($mk as $key => $value) {
	// 	$simpan = new dbMataKuliah;
	// 	$simpan->mk_kode = $value->MK_ID;
	// 	$simpan->mk_nama = $value->MK_Mata_Kuliah;
	// 	$simpan->mk_sks = $value->MK_KreditKuliah;
	// 	$simpan->mk_rmk = $value->MK_RMK_Kode;
	// 	$simpan->save();
	// }
	// echo "string";

	// $alldata = dbDokumen2009::where('judul_ta','=',null)->get();
	// $tiktok =0;
	// foreach ($alldata as $key => $value) {
	// 	//if($value->judul_ta==null){
	// 		echo $value->nrp."<br />";
	// 		$tiktok++;
	// 	//}
	// }
	// echo "---".$tiktok."---";
	//masukkan nrp 2009
	// $monta = DB::table('monta')->get();
	// $cnt = 0;
	// foreach ($monta as $key => $value) {
	// 	if(Dokumen::find($value->nrp)==null){
	// 		$simpan = new dbDokumen2009;
	// 		$simpan->nrp = $value->nrp;
	// 		$simpan->rmk = $value->rmk;
	// 		$simpan->save();

	// 		echo $value->nrp."<br />";
	// 		$cnt++;	
	// 	}
	// }
	// echo "masuk ".$cnt;

	//masukin nama 2009
	// $alldata = dbDokumen2009::all();
	// foreach ($alldata as $key => $value) {
	// 	$data = dbMahasiswa2009::find($value->nrp);
	// 	if($data!=null){
	// 		$update = dbDokumen2009::find($value->nrp);
	// 		$update->nama = $data->MA_NamaLengkap;
	// 		$update->save();
	// 	}
	// }
	// echo "cek dokumen 2009";

	//bersihkan data rbtc dr yg udah ada
	// $alldata = dbRbtc::all();
	// foreach ($alldata as $key => $doc) {
	// 	if(Dokumen::find($doc->nrp)!=null){
	// 		$data = dbRbtc::find($doc->nrp);
	// 		$data->delete();
	// 	}
	// }
	// echo "bersih";

	//tambahkan 2010 yang 3.5 tahun dan abstrak 2009
	// $alldata = dbRbtc::all();
	// $count = 0;
	// foreach ($alldata as $key => $data) {
	// 	if($data->nrp!='null'){
	// 		if(Dokumen::find($data->nrp)==null){
	// 			echo $data->nrp."<br />";
	// 			if(dbDokumen2009::find($data->nrp)==null){
	// 				$simpan = new dbDokumen2009;
	// 				$simpan->nrp = $data->nrp;
	// 				$simpan->nama = $data->nama;
	// 				$simpan->judul_ta = $data->judul;
	// 				$simpan->abstraksi_ta = $data->abstraksi;
	// 				$simpan->save();

	// 				$del = dbRbtc::find($data->nrp);
	// 				$del->delete();
	// 			}elseif (dbDokumen2009::find($data->nrp)!=null) {
	// 				$simpan = dbDokumen2009::find($data->nrp);
	// 				$simpan->nama = $data->nama;
	// 				$simpan->judul_ta = $data->judul;
	// 				$simpan->abstraksi_ta = $data->abstraksi;
	// 				$simpan->save();

	// 				$del = dbRbtc::find($data->nrp);
	// 				$del->delete();
	// 			}
	// 			$count++;
	// 		}
	// 		else{
	// 			$del = dbRbtc::find($data->nrp);
	// 			$del->delete();
	// 			echo $data->nrp."<br />";
	// 		}
	// 	}
	// }
	// echo "yang 3.5 nambah ".$count;
});

//route for trying anything
Route::get('coba', function(){
	//jadiin satu dokumen
	$data = dbDokumen2009::all();
	$c=0;
	foreach ($data as $key => $dt) {
		$cek = Dokumen::find($dt->nrp);
		if($cek==null){
			$tambah = new Dokumen;
			$tambah->nrp = $dt->nrp;
			$tambah->rmk = $dt->rmk;
			$tambah->nama = $dt->nama;
			$tambah->judul_ta = $dt->judul_ta;
			$tambah->abstraksi_ta = $dt->abstraksi_ta;
			$tambah->transkrip = $dt->transkrip;
			$tambah->save();
			$c++;
		}
	}
	echo($c);

	// //get substring
	// echo substr("5110100020", 2, 2);

	//see json data
	// $data = array('nama'	=> 'aida',
	// 			  'nrp'		=> '5111100020',
	// 			  'jk'		=> 'perempuan');
	// echo json_encode($data);
	// echo "<br /> <br />";
	// $dt = '{"nama":"aida","nrp":"5111100020","jk":"perempuan"}';
	// $arr = json_decode($dt);
	// echo $arr->nama."<br />";
	// echo $arr->nrp."<br />";
	// echo $arr->jk."<br />";
	// $docs = array('5111100018', '5111100020', '5110100018');
	// echo json_encode($docs)."<br />";
	// $doc = json_decode('["5111100018","5111100020","5110100018"]');
	// foreach ($doc as $key => $value) {
	// 	echo $value."<br />";
	// }

	//calculate tf-idf
	//tf = jumlah_kemunculan_kata_di_dokumen / jumlah_kata_dalam_dokumen
	// //idf = log(jumlah_dokumen / jumlah_dokumen_yang_mengandung_kata)
	// $tf = (float)((float)3/(float)100);
	// $idf = (float)(log10((float)10000000/(float)1000));
	// echo "tf = ".$tf."<br />";
	// echo "idf = ".$idf."<br />";
	// echo "tf-idf = ".(float)($tf*$idf);

	
	//remove number string
	// $katakata = KamusKata::all();
	// $data = Dokumen::find('5109100005');
	// $abstrak = $data->abstrak_af_preproc;
	// echo $abstrak."<br />"."<br />";
	// $content = explode(" ", $abstrak);
	// $delword = array();
	// foreach ($content as $key => $value) {
	// 	$get_number = preg_replace("/[^0-9]/","",$value);
	// 	if(is_numeric($get_number))
	// 	{
	// 		array_push($delword, $value);
	// 	}
	// }
	// array_push($delword, '0');
	// array_push($delword, '1');
	// array_push($delword, '2');
	// array_push($delword, '3');
	// array_push($delword, '4');
	// array_push($delword, '5');
	// array_push($delword, '6');
	// array_push($delword, '7');
	// array_push($delword, '8');
	// array_push($delword, '9');
	// echo str_replace($delword, '', $abstrak)."<br />"."<br />";

	//checking
	// $data = Dokumen::find('5109100003');
	// var_dump(explode(' ', $data->abstrak_af_preproc));

	//get count of word in string with count the time
	// function microtime_float()
	// {
	// 	list($usec, $sec) = explode(" ", microtime());
	// 	return ((float)$usec + (float)$sec);
	// }
	// $str = "I have three PHP books first one is PHP Tastes Good next is PHP in your breakfast and the last one is PHP Nightmare";
	// $start = microtime_float();
	// for ($i=0; $i<10000; $i++)
	// {

	//     $cnt = substr_count($str, 'HP');
	//     //echo($cnt); echo "<br />";
	// }

	// $end = microtime_float();
	// echo $cnt." Count By substr_count took : ".($end-$start)." Seconds\n";

	//count word in string
	// $dokumen = Dokumen::find('5109100005');
	// $kata2 = str_word_count($dokumen->abstrak_af_preproc,1);

	//sqrt
	//echo sqrt(25);

	//random number
	// for ($i=0; $i <5 ; $i++) { 
	// 	echo(mt_rand(0,186));
	// 	//echo(rand(0,186));
	// 	echo "<br />";
	// }

	//try class
	// $id = new Temp\Identity;
	// //$id->Identity();
	// echo($id->nama);
	// echo($id->nrp);

	//get index of an item in array
	 // $v = array(6.1, 6.2, 6.3, 6.4);
	 // var_dump($v);
	 // echo "<br />";
	// echo(array_search('dua', $v));
	// echo(array_search(max($v), $v));
	 // //reset($v);
	 // $v = array();
	 // echo "string<br />";
	 // var_dump($v);

	//check indentic array
	// $v1 = (object) array('satu'=>'1', 'dua'=>'2', 'tiga'=>'3');
	// $v2 = (object) array('satu'=>'1', 'dua'=>'2', 'tiga'=>'3');
	// $v3 = (object) array('dua'=>'2', 'tiga'=>'3', 'satu'=>'1');
	// $v4 = (object) array('tiga'=>'3', 'satu'=>'1', 'dua'=>'2');

	// $vvv = array();
	// array_push($vvv, $v1);
	// array_push($vvv, $v3);
	// array_push($vvv, $v4);

	// $www = $vvv;

	// //var_dump($www);

	// $vvv[0] = $v1;
	// $vvv[1] = $v1;
	// $vvv[2] = $v1;

	// //var_dump($www);
	// //var_dump($vvv[0]);

	// for ($i=0; $i < count($vvv); $i++) { 
	// 	foreach ($vvv[$i] as $key => $value) {
	// 		var_dump($value);
	// 	}
	// }

	//echo($v1==$v3);
	// if($vvv==$www)
	// 	echo "betul";
	// else
	// 	echo "salah";

	//make a centroid
	// $timer = new TimeExecution;
	// $kmeans = new Kmeans;
	// $start = $timer->getTime();
	// $kmeans->GenerateCentroidMean(3);
	// $end = $timer->getTime();
	// echo "<br />Waktu : ".($end-$start)." detik ";

	//check kmeans result
	//$corpus = Dokumen::all();
	// for ($i=59; $i <=62 ; $i++) {
	// 	$now = KmeansTime::find($i);
	// 	$bef = KmeansTime::find($i-1);
	// 	$v_now = json_decode($now->hasil_kluster);
	// 	$v_bef = json_decode($bef->hasil_kluster);
	// 	echo ($i-1)." dengan ".$i." = ";
	// 	if($v_now==$v_bef)
	// 		echo "SAMA"."<br /><br />";
	// 	else
	// 		echo "NO"."<br /><br />";
	// }

	//get range value biggest
	//$a = array(3,5,6,1,23,6,78,99);
	//$a = array(10,5,9,13,5,20,1,5,3,6,8,0,1,8,6,13);
	// $a = array('tiga'=>'3', 'satu'=>'1', 'dua'=>'2', 'empat'=>'4', 'nol'=>'0', 'delapan'=>'8', 'sepuluh'=>'10');
	// asort($a);
	// $b = array_slice($a, -4);
	// arsort($b);
	// var_dump($b);

	// $a = $timer->getTime();
	// $kmeans = new Kmeans;
	// $kmeans->PickOfTerm(50);
	// $z = $timer->getTime();
	// echo "<br />waktu : ".($z-$a)." detik <br />";

	// $a = array("aida", "muflichah", "aaa");
	// $b = array("aida");
	// var_dump(array_diff($a, $b));
	
	// $dokumen = Dokumen::find('5109100005');
	// // echo "Dokumen teks abstraksi : <br />"."<br />";
	// // echo $dokumen->abstraksi_ta."<br />"."<br />";

	// 	$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
	// 	$stemmer  = $stemmerFactory->createStemmer();
	// 	$afstemming = $stemmer->stem($dokumen->abstraksi_ta);
	// // echo "Dokumen teks setelah Tokenizing dan Stemming : <br />"."<br />";
	// // echo $afstemming."<br />"."<br />";

	// 	$stopwordRemoval= new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
	// 	$removal  = $stopwordRemoval->createStopWordRemover();
	// 	$afremoval = $removal->remove($afstemming);
	// // echo "Dokumen teks setelah Stopword Removal : <br />"."<br />";
	// // echo $afremoval."<br />"."<br />";

	// $afremoval = "0-6 mbps 50 kualitas 6 1-7 5 jadi 70";
	// 	$content = explode(" ", $afremoval);
	// 	$delword = array();
	// 	foreach ($content as $key => $val) {
	// 		$get_number = preg_replace("/[^0-9]/","",$val);
	// 		echo " - |".$get_number." : ";
	// 		if(is_numeric($get_number))
	// 		{
	// 			array_push($delword, $val);
	// 			echo $val."<br />";
	// 		}

	// 	}
	// 	array_push($delword, '0');
	// 	array_push($delword, '1');
	// 	array_push($delword, '2');
	// 	array_push($delword, '3');
	// 	array_push($delword, '4');
	// 	array_push($delword, '5');
	// 	array_push($delword, '6');
	// 	array_push($delword, '7');
	// 	array_push($delword, '8');
	// 	array_push($delword, '9');
	// 	$output = str_replace($delword, '', $afremoval);

	// echo "Dokumen teks setelah penyempurnaan penghilangan karakter dan angka : <br />"."<br />";
	// echo $output."<br />"."<br />";
	 //k=2
	 //v=3

	 // $phisum = array();
	 // $phisum[0] = array(1,2,3);
	 // $phisum[1] = array(1,1,1);
	 // //var_dump($phisum);

	 // $nw = array();
	 // $nw[0] = array(1,2);
	 // $nw[1] = array(3,1);
	 // $nw[2] = array(2,1);
	 // //var_dump($nw);

	 // $nwsum = array(5,10);

	 // // $count = 2;
	 // $arr = array();
	 // for ($i=0; $i <2 ; $i++) { 
	 // 	$arr[$i] = array();
	 // 	for ($j=0; $j <3 ; $j++) { 
	 // 		$arr[$i][$j] = $phisum[$i][$j];
	 // 		//$arr[$i][$j] = $nw[$j][$i]+$nwsum[$i];
	 // 	}
	 // }
	 // //var_dump($arr);

	 // $row = count($arr);
	 // $col = count($arr[0]);
	 // $rra = array();
	 // for ($x=0; $x < $col; $x++) { 
	 // 	$rra[$x] =array();
	 // 	for ($y=0; $y < $row; $y++) { 
	 // 		$rra[$x][$y] = $arr[$y][$x];
	 // 	}
	 // }
	 // var_dump($rra);

	 // // $count2 = 2;
	 // $arrx = array();
	 // for ($j=0; $j <3 ; $j++) { 
	 // 	$arrx[$j]= array();
	 // 	for ($i=0; $i <2 ; $i++) { 	
	 // 		//$arrx[$j][$i] = $nw[$j][$i]+$nwsum[$i];
	 // 		//echo "[".$j."][".$i."] = ".$nw[$j][$i]."+".$nwsum[$i]."<br />";
	 // 		$arrx[$j][$i] = $phisum[$i][$j];
	 // 	}
	 // }
	 // var_dump($arrx);
 });

Route::get('baca', function(){
	$file = fopen("./data/dt_training.txt","r");
	$arrNRP = array();
	while(! feof($file))
	{
		array_push($arrNRP, fgets($file));
	}

	fclose($file);
	var_dump($arrNRP);
	foreach ($arrNRP as $key => $value) {
		echo $value."<br />";
	}

	// $contents = File::get("./data/dt_training.txt");
	// //var_dump($contents);
	// echo $contents."<br />";
	// $arrNRP = explode("/n", $contents);
	// foreach ($arrNRP as $key => $value) {
	// 	echo $value.'-';
	// }
});
	
?>