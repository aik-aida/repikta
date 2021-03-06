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

Route::get('hai', function(){
	return View::make('tabpanel');
});

Route::get('/', 'BaseController@home');
Route::post('transkrip', 'RepiktaController@read_transkrip');
Route::post('rekomendasi', 'RepiktaController@rekomendasi');
Route::get('rekomendasi/dokumen/{param}/{klp}', 'RepiktaController@rekomendasi_katadokumen');
Route::post('rekomendasi/dokumen/detail', 'RepiktaController@rekomendasi_dokumen');

Route::post('admin', 'BaseController@login');

Route::get('dashboard', 'AdminController@dashboard');
Route::get('dashboard/dokumen', 'AdminController@dashboard_dokummen');
Route::post('dashboard/transkrip', 'AdminController@dashboard_transkrip');
Route::post('dashboard/topik', 'AdminController@dashboard_topik');

Route::get('centroid', 'AdminController@centroid_list');
Route::post('centroid/detail', 'AdminController@centroid_detail');

Route::get('kamus', 'AdminController@kamus_list');

Route::get('dokumen', 'AdminController@dokumen_list');
Route::post('dokumen/detail', 'AdminController@dokumen_detail');
Route::post('dokumen/nilai_tf', 'AdminController@dokumen_tf');
Route::post('dokumen/nilai_tfidf', 'AdminController@dokumen_tfidf');

Route::get('kluster', 'AdminController@kluster_list');
Route::post('kluster/detail', 'AdminController@kluster_detail');

Route::get('testing', 'AdminController@testing_list');
Route::post('testing/transkrip', 'AdminController@testing_transkrip');
Route::post('testing/rekomendasi', 'AdminController@testing_rekomendasi');
Route::get('testing/dokumen/{param}/{klp}', 'AdminController@testing_rekomendasi_katadokumen');
Route::post('testing/dokumen/detail', 'AdminController@testing_rekomendasi_dokumen');

Route::get('survey', 'AdminController@survey_login');
Route::post('survey/penjelasan', 'AdminController@survey_penjelasan');
Route::post('survey/dokumen', 'AdminController@survey_dokumen');
Route::post('survey/dokumen/nilai', 'AdminController@survey_nilai');
Route::post('survey/masukan', 'AdminController@survey_masukan');
Route::post('survey/selesai', 'AdminController@survey_selesai');

Route::get('akurasi', 'AdminController@akurasi');

Route::get('lihatPHI', 'RepiktaController@show_phi');
Route::get('lihatTETA', 'RepiktaController@show_theta');

Route::get('testLDA', function(){
	$testing = new Testing;
	//$data_id = $testing->GetDataTestingLDA(41); 
	//var_dump($data);
	//$data_matrix = $testing->GetMatriksDataTest($data_id);
	//var_dump($data_matrix);
	$id_arr = array(1,2,3);
	for ($i=0; $i <3 ; $i++) { 
		$perplexity = $testing->PerplexityLDA( 1, 41, $i);
		$update = dbLdaSave::find($id_arr[$i]);
		$update->perplexity = $perplexity;
		$update->save();
	}
	echo "check!!!";
});

Route::get('survey_build', function(){
	$lda_percobaan_ke = 19;
	$treshold = 0.62;
	$dokumen_survey = dbTestingAkurasi::where('lda_ke','=',$lda_percobaan_ke)
									->orderBy('cosine_similarity_20', 'desc')->take(10)->get();
	$dokumen_survey_nrp = array();
	$dokumen_survey_data = array();

	foreach ($dokumen_survey as $key => $doc) {
		array_push($dokumen_survey_nrp, $doc->nrp_testing);
		$data_survey = dbTestingRekomendasi::where('id_lda','=',$lda_percobaan_ke)
											->where('nrp_testing','=',$doc->nrp_testing)
											->get();
		array_push($dokumen_survey_data, $data_survey[0]);
	}
	var_dump($dokumen_survey_nrp);
	// foreach ($dokumen_survey_nrp as $key => $value) {
	// 	$doc = dbDokumen::find($value);
	// 	echo $doc->judul_ta."<br />";
	// }
	$simpan_survey = new dbSurveyDaftar;
	$simpan_survey->id_group_lda = $lda_percobaan_ke;
	$simpan_survey->dokumen_survey = json_encode($dokumen_survey_nrp);
	$simpan_survey->save();
});

Route::get('rekomendasi', function(){
	$nrp = '5109100003';
	$main = new Repikta;
	$kluster_topkri = $main->RekomendasiTopik($nrp);
	//var_dump($kluster_topkri);

	$testing = new Testing;
	$topikVektor = $testing->TopikVektor($kluster_topkri[0], $kluster_topkri[1]);

	$mahasiswa = dbDokumen::find($nrp);
	$dokumenVektor = json_decode($mahasiswa->nilai_tfidf);
	$akurasi = $testing->CosineValRekomendasi($topikVektor, $dokumenVektor);
	echo $nrp." --- ".$akurasi." ---<br />";
});

Route::get('catat', function(){
	$pra = new Testing;
	//$pra->TfIdf(0.2,0.8);

	$current_use = dbCurrentUse::all();
	$idgroup_result = $current_use[0]->id_kluster;
	$id_hasil_lda = $current_use[0]->id_lda;
	echo "ID LDA = ".$id_hasil_lda."<br />";
	// $idgroup_result = 1;
	// $id_hasil_lda = 1;


	$dokumens = dbDokumen::where('training','=',false)->get();
	$array_hasil = array();
	foreach ($dokumens as $key => $mhs) {

		$nrp = $mhs->nrp;

		$hasil = (object) array();
		$hasil->nrp = $nrp;

		$main = new Repikta;
		$kluster_topkri = $main->RekomendasiTopik($nrp, $idgroup_result, $id_hasil_lda);
		$testing = new Testing;
		$topikVektor = $testing->TopikVektor($kluster_topkri[0], $kluster_topkri[1], $id_hasil_lda);
		
		$mahasiswa = dbDokumen::find($nrp);
		$dokumenVektor = json_decode($mahasiswa->nilai_tfidf);
		//echo count($dokumenVektor)." - ".count($topikVektor),"<br />";


		$rekom = dbTestingRekomendasi::where('id_lda','=',$id_hasil_lda)
									->where('nrp_testing','=',$nrp)->get();
		$bidang = $rekom[0]->kluster_bidang;
		$k= $rekom[0]->k_topik;
		$daftar = json_encode($rekom[0]->daftar_topik_terpilih);
		$id_data = $rekom[0]->id;

		$muncul_nilai = $testing->Kemunculan($id_hasil_lda, $bidang, $k, $daftar, $mahasiswa->judul_af_preproc, $dokumenVektor);
		$update = dbTestingRekomendasi::find($id_data);
		$update->kemunculan_judul = $muncul_nilai[0];
		$update->nilai_judul = $muncul_nilai[1];
		$update->kemunculan_tfidf20 = $muncul_nilai[2];
		$update->nilai_tfidf20 = $muncul_nilai[3];
		$update->save();

		$hasil->muncul_judul = $muncul_nilai[0];
		$hasil->nilai_judul = $muncul_nilai[1];
		$hasil->muncul_tfidf = $muncul_nilai[2];
		$hasil->nilai_tfidf = $muncul_nilai[3];

		$akurasi20 = $testing->CossineRekomendasi20($topikVektor, $dokumenVektor);
		$akurasi = $testing->CosineValRekomendasi($topikVektor, $dokumenVektor);

		$hasil->similarity_all = $akurasi;
		$hasil->similarity_tfidf = $akurasi20;


		$simpan = new dbTestingAkurasi;
		$simpan->lda_ke = $id_hasil_lda;
		$simpan->nrp_testing = $nrp;
		$simpan->topik_vektor = json_encode($topikVektor);
		$simpan->cosine_similarity = $akurasi;
		$simpan->cosine_similarity_20 = $akurasi20;
		$simpan->save();

		array_push($array_hasil, $hasil);
	}

	foreach ($array_hasil as $key => $value) {
		echo "[1]  ".$value->nrp."&nbsp;&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;&nbsp;".$value->similarity_all."<br />";
	}echo "<br /><br /><br />";
	foreach ($array_hasil as $key => $value) {
		echo "[2]  ".$value->nrp."&nbsp;&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;&nbsp;".$value->similarity_tfidf."<br />";
	}echo "<br /><br /><br />";
	foreach ($array_hasil as $key => $value) {
		echo "[3]  ".$value->nrp."&nbsp;&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;&nbsp;".$value->muncul_judul."&nbsp;&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;&nbsp;".$value->nilai_judul."<br />";
	}echo "<br /><br /><br />";
	foreach ($array_hasil as $key => $value) {
		echo "[4]  ".$value->nrp."&nbsp;&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;&nbsp;".$value->muncul_tfidf."&nbsp;&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;&nbsp;".$value->nilai_tfidf."<br />";
	}echo "<br /><br /><br />";
});

Route::get('dokumen_terdekat', function(){
	$repikta = new Repikta;
	$repikta->GetClosest();
});

Route::get('hasil_survey', function(){
	$data_survey = dbSurveyData::all();
	$data_penguji = dbSurveyData::select('nrp_penguji')->distinct()->get();
	$daftar_data = dbSurveyDaftar::find(1);
	$data_test = json_decode($daftar_data->dokumen_survey);
	echo "<table>";
	echo "<tr>";
	echo "<td>penguji</td>";
	foreach ($data_test as $key => $value) {
		echo "<td>";
		echo $value;
		echo "</td>";
	}
	echo "<tr>";
	foreach ($data_penguji as $key => $value) {
		$nrp = $value->nrp_penguji;
		if($nrp!='5111100020' && $nrp!='5113100039'){
			echo "<tr>";
					echo "<td>";
					echo $nrp;
					echo "</td>";
				foreach ($data_test as $key => $dokumen) {
					$data_hasil = dbSurveyData::where('nrp_penguji','=',$nrp)
											->where('dokumen','=',$dokumen)->get();
					echo "<td>";
					echo $data_hasil[0]->nilai;
					echo "</td>";
				}
			echo "</tr>";
		}
	}
	echo "</table>";
	
});

Route::get('view_perplexity', function(){
	$kelompok = 2;
	$data = dbLdaSave::where('percobaan_ke','>',20)
		->where('kluster_ke','=',$kelompok)
		->orderBy('k_topik', 'asc')
		->get();
	foreach ($data as $key => $testing) {
		echo $testing->k_topik." - ".$testing->perplexity."<br />";
	}


	// $testing = new Testing;
		
			
	// 			$perplexity = $testing->PerplexityLDA( 1, 41, 0);

	// 			$lda_result = dbLdaSave::where('percobaan_ke','=',1)
	// 							->where('kluster_ke','=',0)
	// 							->where('group','=',41)->get();
	// 			$id_last = $lda_result[0]->id;
	// 			$update = dbLdaSave::find($id_last);
	// 			$update->perplexity = $perplexity;
	// 			$update->save();
	// 			echo $perplexity."<br />";
});

Route::get('stopwordLDA', function(){
	$all = dbDokumen::all();
	foreach ($all as $key => $value) {
		$dokumen = dbDokumen::find($value->nrp);
		$teks = $dokumen->abstrak_af_preproc;
		$judul = $dokumen->judul_af_preproc;

		//stopword removal sastrawi
		$stopwordRemoval= new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
		$removal  = $stopwordRemoval->createStopWordRemover();
		$teks_afremoval = $removal->remove($teks);
		$judul_afremoval = $removal->remove($judul);

		$dokumen->judul_for_lda = $judul_afremoval;
		$dokumen->abstrak_for_lda = $teks_afremoval;
		$dokumen->save();

		// $katajudul = explode(' ', $judul_afremoval);
		// $katakata = explode(' ', $teks_afremoval);

		// $N = count($katakata);
		// $jumlahjudul = count($katajudul);

	}
});


Route::get('ekstrak_topik', function(){
	//HASIL CLUSTERNG YANG DIPAKAI, ID 42 , ID_GROUP 41 -- traingin 240
	//HASIL CLUSTERNG YANG DIPAKAI, ID 4 , ID_GROUP 1 -- traingin 80
	//HASIL CLUSTERNG YANG DIPAKAI, ID  , ID_GROUP  -- traingin 160
	$current_use = dbCurrentUse::all();
	$id_group = $current_use[0]->id_kluster;

	// $counter = new TimeExecution;
	// $awal = $counter->getTime();

	$masing2topik = array(3,5, 4);
	// $masing2topik = array(5,3,4);
	
	$id_result = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
	//echo "Group=".$id_group." - id_result=".$id_result."<br />";
	$data_result = dbKmeansResult::find($id_result);
	$banyak_kluster = $data_result->jumlah_kluster;
	$hasil_kluster = json_decode($data_result->hasil_kluster);

	$testing = new Testing;
	// 19
	// for ($k=1; $k <=10 ; $k++) { 
		$maxID = DB::table('lda_saved')->max('percobaan_ke');
		$no_percobaan = ($maxID+1);

		for ($i=0; $i <$banyak_kluster ; $i++) { 
			$lda = new LdaGibbsSampling();
			// if($k<=count($hasil_kluster[$i])){
				$k = $masing2topik[$i];
				$lda->TopicExtraction($k, $hasil_kluster[$i], $id_result, $i, $id_group, $no_percobaan);
				$perplexity = $testing->PerplexityLDA( $no_percobaan, $id_group, $i);

				$id_last = DB::table('lda_saved')->max('id');
				$update = dbLdaSave::find($id_last);
				$update->perplexity = $perplexity;
				$update->save();
				echo "k_topik=".$k." - kluster_ke".$i." : ".$perplexity."<br />";
			// }
		}
	// }

	// $akhir = $counter->getTime();
	// $lama = ($akhir-$awal);
	// echo "SUDAH BISA DILIHAT HASIL LDA-NYA : ".$id_group." , ".$id_result."<br />"." lama : ".$lama." detik <br />";

	// echo "----------------------------------------------------THETA-----------------------------------------------<br />";
	// $theta = $lda->theta;
	// foreach ($theta as $key => $value) {
	// 	foreach ($value as $key => $val) {
	// 		echo $val." , ";
	// 	}
	// 	echo "<br />";
	// }
	// echo "<br /><br /><br />";
	// echo "----------------------------------------------------PHIII-----------------------------------------------<br />";
	// $phi = $lda->phi;
	// foreach ($phi as $key => $value) {
	// 	foreach ($value as $key => $val) {
	// 		echo $val." , ";
	// 	}
	// 	echo "<br />";
	// }
});

Route::get('tolong', function(){

 	$vocab = array();
			$temp_vocab = array();
			$kamuskata = dbKamusKata::get();

			foreach ($kamuskata as $key => $datakata) {
				array_push($vocab, $datakata->kata_dasar);
			}

			
 	echo "bismillah";
 	$dokumen = dbDokumen::find('5109100010');
 	$katakata = explode(' ', $dokumen->abstrak_af_preproc);
				$N = count($katakata);
				for ($n=0; $n < $N; $n++) { 
					$term = $katakata[$n];
					// if(in_array($term, $intijudul)){
						$idx = array_search(trim($term), $vocab);
						echo $idx." - ".(strlen(trim($term)))."<br />";
						if(strlen(trim($term)!=0)){
						// 	$kataKamus = dbKamusKata::find($term);
						// 	if($kataKamus->jumlah_dokumen<=43){
								 	echo "string";
									//array_push($this->corpus[$m], $idx);
								
						// 	}
						}
					// }
				}
 });

Route::get('clustering',function(){
	$counter = new TimeExecution;
	$startTime = $counter->getTime();

	$doc_training = dbDokumen::where('training','=',true)->get();
		// $centroid_choose = 2; //MANUAL CENTROID ID
		// $number_k = 
		
	$n = count($doc_training);
	echo "dokumen training = ".$n."<br />";

	// $k_generated = array(3,6,8,10,16);
	// foreach ($k_generated as $key => $value) {
	//$k_manual = array(8,6,3);
	//foreach ($k_manual as $key => $value) {
		$kmeans = new Kmeans;
		//$k = $value; 
		$k = 3;
		echo "n=".$n." - k=".$k."<br />"."<br />";

		//a:abstrak
		//j:judul
		//ja:judul+abstrak
		//g:generated
		//m:manual
		$id_group = $kmeans->Clustering($k, $n, 'ja', 'm');
		//$id_group = $kmeans->Clustering($k, $n, 'ja', 'g');
		
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

		//--Transkrip Kriteria
		$rk = new Repikta;
		$rk->Generate_Transkrip_Kriteria($id_group);
		echo "Transkrip Kriteria done";

		//--Cluster Varian--
		$counter_CV = new TimeExecution;
		$startTime_CV = $counter_CV->getTime();
		$KedekatanKluster = new ClusterVariance;
		// $id_group = 1;
		$id = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
		echo $id."<br />";
		$hasil = dbKmeansResult::find($id);
		echo $hasil->jumlah_kluster."<br />";
		$hasil_kluster = json_decode($hasil->hasil_kluster);

		$nilai_dekat = $KedekatanKluster->ClusterValue($hasil->jumlah_kluster, $hasil_kluster, $id, 'ja');
		$endTime_CV = $counter_CV->getTime();
		echo $nilai_dekat." - LAMA : ".($endTime_CV-$startTime_CV)." detik <br />";
	//}
});

Route::get('cv', function(){
	//--Cluster Varian--
	$counter_CV = new TimeExecution;
	$startTime_CV = $counter_CV->getTime();
	$KedekatanKluster = new ClusterVariance;
	$id_group = 9;
	$id = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
	echo $id."<br />";
	$hasil = dbKmeansResult::find($id);
	echo $hasil->jumlah_kluster."<br />";
	$hasil_kluster = json_decode($hasil->hasil_kluster);

	$nilai_dekat = $KedekatanKluster->ClusterValue($hasil->jumlah_kluster, $hasil_kluster, $id, 'j');
	$endTime_CV = $counter_CV->getTime();
	echo $nilai_dekat." - LAMA : ".($endTime_CV-$startTime_CV)." detik <br />";
});

Route::get('inisial', function(){
	$counter = new TimeExecution;
	$time1 = $counter->getTime();

	$prepoc = new Preprocessing;
	$prepoc->Reset_TfIdf();
	$prepoc->Reset_Idf();

	// $tipe_centroid = 'a';
	$time2 = $counter->getTime();
	// $file_cent = $prepoc->ReadFile("./data/dt_centroid.txt");
	// $dt_cent = $prepoc->ReadCentroid($file_cent);
	$dt_train = $prepoc->ReadFile("./data/dt_training.txt");
	$dt_test = $prepoc->ReadFile("./data/dt_testing.txt");
	$prepoc->Set_training_testing($dt_train, $dt_test);

	
	$time3 = $counter->getTime();
	$prepoc->PreprocessingText(); $time4 = $counter->getTime();
	$prepoc->DistinctTerm(); $time5 = $counter->getTime();

	$prepoc->CountIDF(); $time6 = $counter->getTime();
	$prepoc->CountTF(); $time7 = $counter->getTime();
	$prepoc->CountTF_IDF(); $time8 = $counter->getTime();
	// $prepoc->MinMaxIDF(); 


	$prepoc->PembobotanTF_IDF(0.2,0.8); $time9 = $counter->getTime();
	// $prepoc->Calculate_Save_Centroid($dt_cent, $tipe_centroid);
	
	echo "preparing data : ".($time3-$time2)."detik <br />";	
	echo "Preprocessing text : ".($time4-$time3)."detik <br />";	
	echo "distinct term : ".($time5-$time4)."detik <br />";	
	echo "hitung IDF : ".($time6-$time5)."detik <br />";	
	echo "hitung TF : ".($time7-$time6)."detik <br />";	
	echo "hitung TF-IDF : ".($time8-$time7)."detik <br />";	
	echo "Pembobotan : ".($time9-$time8)."detik <br />";	
	echo "done";
});

Route::get('reset_dokumen', function(){
	$dokumens = dbDokumen::all();
	foreach ($dokumens as $key => $doc) {
		$update = dbDokumen::find($doc->nrp);
		$update->training = -1;
		$update->save();
	}
	echo "cek cek cek :)";
});

Route::get('transkrip_kriteria', function(){
	//HASIL CLUSTERNG YANG DIPAKAI, ID 42 , ID_GROUP 41 -- traingin 240
	//HASIL CLUSTERNG YANG DIPAKAI, ID 4 , ID_GROUP 1 -- traingin 80
	//HASIL CLUSTERNG YANG DIPAKAI, ID  , ID_GROUP  -- traingin 160
	$id_group = 1;
	$rk = new Repikta;
	$rk->Generate_Transkrip_Kriteria($id_group);
	echo "Transkrip Kriteria done";
});

Route::get('gettranskrip', function(){
	$nrps = dbDokumen::select('nrp')->get();
	foreach ($nrps as $key => $value) {
		//echo $value->nrp."<br />";
		$nrp = $value->nrp;
		$json_transkrip = (object) array();
		$matakuliah = dbMataKuliah::all();
		foreach ($matakuliah as $key => $mk) {
			$kode = $mk->mk_kode;
			$nilai = dbTranskrip::where('nrp', '=', $nrp)->where('mk_id', '=', $kode)->get();
			if(count($nilai)==1){
				$huruf = $nilai[0]->nilai_huruf;
				$angka = -1.0;
				switch ($huruf) {
					case 'A':
						$angka = 4.0;
						break;
					case 'AB':
						$angka = 3.5;
						break;
					case 'B':
						$angka = 3;
						break;
					case 'BC':
						$angka = 2.5;
						break;
					case 'C':
						$angka = 2;
						break;
					case 'D':
						$angka = 1;
						break;
					case 'E':
						$angka = 0;
						break;
				}

				$json_transkrip->$kode = $angka;
			}
			elseif(count($nilai)==0){
				$json_transkrip->$kode = 0.0;
			}
			elseif(count($nilai)>1){
				echo $value->nrp." - ";
				echo $mk->mk_kode." - ";
				echo "masih ada yang double, aaaak !!!<br />";
			}
		}

		$transkrip = json_encode($json_transkrip);
		//echo  $transkrip."<br />";

		$update = dbDokumen::find($nrp);
		$update->transkrip = $transkrip;
		$update->save();
	}
	echo "SELESAI<br />";
});

Route::get('all_distance',function(){
	$dokumen_testing = dbDokumen::where('training','=',false)->get();
	$dokumen_training = dbDokumen::where('training','=',true)->get();
	echo "testing : ".count($dokumen_testing)."<br />";
	echo "training : ".count($dokumen_training)."<br />";
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

Route::get('distance_list_kluster_terdekat', function(){
	$id_group = 1;
	$dokumen_testing = dbDokumen::where('training','=',false)->get();
	$id_result = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
	$data_kluster = dbKmeansResult::find($id_result);
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
			$pembanding_doc = dbDokumen::find($nrppembanding);
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

 Route::get('distanceAHLI_list_kluster_terdekat', function(){
 	echo "ahli<br />";
	$id_group = 41;
	$dokumen_testing = dbDokumen::where('training','=',false)->get();
	$id_result = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
	$data_kluster = dbKmeansResult::find($id_result);
	$hasil_kluster = json_decode($data_kluster->hasil_kluster);
	$repikta = new Repikta;
	for ($i=0; $i <count($dokumen_testing) ; $i++) { 
		$counter = new TimeExecution;
		$startTime = $counter->getTime();
		$nrp = $dokumen_testing[$i]->nrp;
		echo $nrp."<br />";
		$nrp_tr = json_decode($dokumen_testing[$i]->transkrip);
		$idk = $repikta->Choose_Kluster($nrp_tr, $id_group);
		echo "dekat ".$idk."<br /> ";

		$distance = array();
		foreach ($hasil_kluster[$idk] as $key => $nrppembanding) {
			$counter_t = new TimeExecution;
			$startTime_t = $counter_t->getTime();
			$pembanding_doc = dbDokumen::find($nrppembanding);
			
			$pembanding_tr = json_decode($pembanding_doc->transkrip);
			$dist = $repikta->EuclideanTranskripAhli($nrp_tr, $pembanding_tr);
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

Route::get('masukkan', function(){
	$cv_aik = DB::table('cluster_variance_atud')->get();
	foreach ($cv_aik as $key => $value) {
		//echo $value->keterangan;
		$bobot = $value->keterangan;

		$id_result = $value->id_hasil_kluster;
		$result = DB::table('kmeans_result_atud')->where('id', '=' , $id_result)->get();
		$id_group = $result[0]->id_group;
		

		//echo $id_group;
		$detail_result = DB::table('kmeans_result_atud')->where('id_group', '=' , $id_group)->get();
		foreach ($detail_result as $key => $val) {
			//echo $val->id."<br />";
			$saveKmeans = new dbKmeansResult();
			$saveKmeans->bobot = $bobot;
			$saveKmeans->teks = $val->teks;
			$saveKmeans->centroid = $val->centroid;
			$saveKmeans->id_group = $val->id_group;
			$saveKmeans->jumlah_kluster = $val->jumlah_kluster;
			$saveKmeans->centroid_awal = $val->centroid_awal;
			$saveKmeans->centroid_step = $val->centroid_step;
			$saveKmeans->centroid_next = $val->centroid_next;
			$saveKmeans->jumlah_dokumen = $val->jumlah_dokumen;
			$saveKmeans->hasil_kluster = $val->hasil_kluster;
			$saveKmeans->keterangan_iterasi = $val->keterangan_iterasi;
			$saveKmeans->lama_eksekusi = $val->lama_eksekusi;
			$saveKmeans->waktu_simpan = $val->waktu_simpan;
			$saveKmeans->save();
		}

		$max = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');

		$simpan = new dbClusterVariance;
		$simpan->k = $value->k;
		$simpan->keterangan = $value->keterangan;
		$simpan->id_hasil_kluster = $max;
		$simpan->dmasing2 = $value->dmasing2;
		$simpan->drata2 = $value->drata2;
		$simpan->varian_within = $value->varian_within;
		$simpan->varian_between = $value->varian_between;
		$simpan->cluster_variance = $value->cluster_variance;
		$simpan->save();
	}
	echo "cek cek cek";
});
	
Route::get('cek',function(){
	//<-- cek RMK dokumen testing
	// $hasil_rekomendasi = dbTestingRekomendasi::all();
	// foreach ($hasil_rekomendasi as $key => $value) {
	// 	//echo $value->nrp_testing."<br />";
	// 	// echo $value->kluster_bidang."<br />";
	// 	$nrp = $value->nrp_testing;
	// 	$dokumen = dbDokumen::find($nrp);
	// 	echo $dokumen->kode_rmk."<br />";
	// }

	// <-- dapatkan bidang ilmu dengan distance
	$current_use = dbCurrentUse::all();
	$id_group = $current_use[0]->id_kluster;
	$dokumen_testing = dbDokumen::where('training','=',false)->get();
	$repikta = new Repikta;
	foreach ($dokumen_testing as $key => $value) {
		$nrp = $value->nrp;
		$dokumen = dbDokumen::find($nrp);
		$mhs_transkrip = json_decode($dokumen->transkrip);
		$data = dbTranskripKriteria::where('group','=',$id_group)->get();			
		$arrTranskrip = json_decode($data[0]->kriteria_transkrip);
		$distance = array();
		for ($i=0; $i < count($arrTranskrip); $i++) { 
			$d = $repikta->EuclideanTranskrip($arrTranskrip[$i], $mhs_transkrip);
			$distance[$i] = $d;
		}
		$index = array_search(min($distance), $distance);
		//echo $nrp." - ".$index." - ".(min($distance))."<br />";
		echo $nrp." - ".($distance[2])."<br />";
	}

	//<-- lihat kata penting dalam judul
	// $all_judul = dbKamusJudul::all();	
	// foreach ($all_judul as $key => $value) {
	// 	if($value->jumlah_dokumen<=10){
	// 		$katakamus = dbKamusKata::find($value->kata_dasar);
	// 		echo $value->kata_dasar." - ".$value->jumlah_dokumen." - ".$katakamus->jumlah_dokumen."<br />";
	// 	}
	// }

	













	// $dokumens = dbDokumen::where('training','=',false)->get();
	// foreach ($dokumens as $key => $dokumen) {
	// 	$tfidf = json_decode($dokumen->nilai_tfidf);
	// 	$sum = 0.0;
	// 	foreach ($tfidf as $key => $value) {
	// 		$sum += $value;
	// 	}
	// 	echo $dokumen->nrp." - jumlah tfidf = ".$sum."<br />";
	// }
	//$dokumen = dbDokumen::find('5109100003');
	//$tfidf = json_decode($dokumen->nilai_tfidf);
	//$tfidf = json_decode($dokumen->nilai_tfidf_judul);
	//$tfidf = json_decode($dokumen->nilai_tf_abstrak);
	// $sum = 0.0;
	// foreach ($tfidf as $key => $value) {
	// 	$sum += $value;
	// }
	// echo "jumlah tfidf = ".$sum;
	// $tfidf = (object) array();
	// $term = 'pilih';
	// $tfidf->$term = 0.0089285714285714;
	// echo $tfidf->$term."<br />";
	//var_dump($tfidf);

	// $id_group = 1;
	// $id_result = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
	// $data_kluster = dbKmeansResult::find($id_result);
	// $hasil_kluster = json_decode($data_kluster->hasil_kluster);

	// $id_group2 = 2;
	// $id_result2 = DB::table('kmeans_result')->where('id_group', '=' , $id_group2)->max('id');
	// $data_kluster2 = dbKmeansResult::find($id_result2);
	// $hasil_kluster2 = json_decode($data_kluster2->hasil_kluster);
	// if($hasil_kluster == $hasil_kluster2)
	// 	echo "SAMAAAAA";
	// else
	// 	echo "NO :(";

	// $data = dbTestingAkurasi::all();
	// foreach ($data as $key => $value) {
	// 	echo $value->cosine_similarity."<br />";
	// }

	// $data = dbKamusKata::all();
	// foreach ($data as $key => $value) {
	// 	$indoc = json_decode($value->indoc);
	// 	$update = dbKamusKata::find($value->kata_dasar);
	// 	$update->jumlah_dokumen = count($indoc);
	// 	$update->save();
	// 	echo $value->cosine_similarity."<br />";
	// }
});

?>



