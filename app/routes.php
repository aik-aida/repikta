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
//get TA data (sw 110 111)

Route::get('clustering',function(){
	$counter = new TimeExecution;
	$startTime = $counter->getTime();
	
	$kmeans = new Kmeans;
	$k = 5;
	$n = 20;
	echo "n=".$n." - k=".$k."<br />"."<br />";
	$kmeans->Clustering($k, $n);
	
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
});

Route::get('ekstrak_topik', function(){
	$counter = new TimeExecution;
	$startTime = $counter->getTime();
	$k = 10;
	$lda = new LdaGibbsSampling();
	$lda->TopicExtraction($k);
	//var_dump($lda->docName);
	//echo (array_search('aida', $lda->vocab));
	//echo($lda->Mdoc);
	// $d = Dokumen::find('5109100003');
	// echo($d->abstrak_af_preproc);
	// var_dump($lda->corpus[0]);
	$endTime = $counter->getTime();
	echo ($endTime-$startTime)."detik <br />";
});

Route::get('data', function()
{
	
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
	}
});

//preprocessing all dokumen
Route::get('preprocessing', function()
{
	//mengambil seluruh data dokumen
	$dokumens = Dokumen::all();

	//pra proses untuk setiap dokumen
	foreach ($dokumens as $key => $value) {
		$teks = (object) array("input","afstemming","afremoval","output");
		$teks->input = $value->abstraksi_ta;

		//tokenizing dan stemming sastrawi
		$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
		$stemmer  = $stemmerFactory->createStemmer();
		$teks->afstemming = $stemmer->stem($teks->input);

		//stopword removal sastrawi
		$stopwordRemoval= new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
		$removal  = $stopwordRemoval->createStopWordRemover();
		$teks->afremoval = $removal->remove($teks->afstemming);

		//prose tambahan penghilangan teks angka
		$content = explode(" ", $teks->afremoval);
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
		$teks->output = str_replace($delword, '', $teks->afremoval);

		//menyimpan hasil pra proses dokumen
		$update_preprocessing = Dokumen::find($value->nrp);
		$update_preprocessing->abstrak_af_preproc = $teks->output;
		$update_preprocessing->save();
	}
});

//get distinct term in all document
Route::get('getwords', function()
{
	
	//$dokumen = Dokumen::find('5109100005');
	//$dokumen = Dokumen::all();
	//echo $dokumen->abstrak_af_preproc."<br />"."<br />";
	// foreach ($dokumen as $key => $data) {
	// 	$words = explode(' ', $dokumen->abstrak_af_preproc);
	// 	foreach ($words as $key => $kata) {
	// 		if(KamusKata::find($kata)==null)
	// 			array_push($new_words, $kata);
	// 	}
	// }

	//$new = array();
	$count = 0;
	$dokumen = Dokumen::all();
	for ($i=0; $i < 187	; $i++) { 
		echo $i."-".$dokumen[$i]->nrp."<br />";
		$words = explode(' ', $dokumen[$i]->abstrak_af_preproc);
		foreach ($words as $key => $kata) {
			if(KamusKata::find($kata)==null){
				//array_push($new, $kata);
				$check = new KamusKata;
				$check->kata_dasar = $kata;
				$check->save();
				$count++;
			}
		}
	}
	//var_dump($new);
	echo($count);

	// $distinct_word = CekAbstrak::all();
	// foreach ($distinct_word as $key => $value) {
	// 	$word = new KamusKata;
	// 	$word->kata_dasar = $value->kata;
	// 	$word->save();
	// 	//CekAbstrak::destroy($value->id);
	// 	//echo "delete ".$value->id."<br />";
	// }
});

Route::get('count_idf',function()
{
	$dokumens = Dokumen::all();
	$words = KamusKata::all();
	foreach ($words as $key => $kata) {
		echo($kata->kata_dasar);
		echo "<br />";
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

				$kamus = KamusKata::find($kata->kata_dasar);
				$kamus->idf = $idf;
				$kamus->indoc = $iddoc;
				$kamus->save();
				echo "string<br />";
		}
	}
	echo "yeeee bismillah bener !!!";
});

Route::get('count_tf', function()
{
	$dokumens = Dokumen::all();
	$words = KamusKata::all();
	//$dokumen = Dokumen::find('5109100003');
	foreach ($dokumens as $key => $dokumen) {
		$tfvector = array();
		foreach ($words as $key => $kata) {
			$nword = substr_count($dokumen->abstrak_af_preproc, ' '.$kata->kata_dasar.' ');
			$nall = str_word_count($dokumen->abstrak_af_preproc,0);
			$tfvector[$kata->kata_dasar] = (float)((float)$nword/(float)$nall);			
		}
		$doc = Dokumen::find($dokumen->nrp);
		$doc->nilai_tf = json_encode($tfvector);
		$doc->save();
		echo $dokumen->nrp."<br />";
	}
	echo "alhamdulillah";
});

Route::get('tf-idf', function(){
	$dokumens = Dokumen::all();
	$words = KamusKata::all();
	//$dokumen = Dokumen::find('5109100003');
	foreach ($dokumens as $key => $dokumen) {
		$tfidf = array();
		$tf = json_decode($dokumen->nilai_tf);
		//var_dump($tf->orang);
		//echo $tf->"1";
		foreach ($words as $key => $kata) {
			$term = $kata->kata_dasar;
			//echo $tf->$term."<br />";	
			//echo $tf->$term." * ".$kata->idf." = ";
			$tfidf[$kata->kata_dasar] = (float)((float)($tf->$term)*(float)($kata->idf));
			//echo $tfidf[$kata->kata_dasar]."<br />";
		}
		$doc = Dokumen::find($dokumen->nrp);
		$doc->nilai_tfidf = json_encode($tfidf);
		$doc->save();
		echo $dokumen->nrp."<br />";
	}
	echo "alhamdulillah";
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
		foreach ($corpus as $key => $doc) {
			$vector = json_decode($doc->nilai_tfidf);
			array_push($minmax , $vector->$term);
		}
		echo($count++); echo " - "; echo($term); echo "<br />";
		//var_dump($minmax);
		//echo(count($minmax));
		//echo($term);

		$updateMinMax = KamusKata::find($term);
		$updateMinMax->min_value = min($minmax);
		$updateMinMax->max_value = max($minmax);
		$updateMinMax->save();
	}
	$Z = $timer->getTime();
	echo "sudaaah, yeay! ".($Z-$A)." detik";
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

<<<<<<< HEAD
Route::get('clustering',function(){
	$counter = new TimeExecution;
	$startTime = $counter->getTime();
	
	$kmeans = new Kmeans;
	$k = 20;
	$n = 187;
	echo "n=".$n." - k=".$k."<br />"."<br />";
	$kmeans->Clustering($k, $n);
	
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
	

	// var_dump($kmeans->prevCentroid==$kmeans->centroid);
	// echo "<br />";
	// var_dump($kmeans->prevCentroid);
	// echo "<br />";
	// var_dump($kmeans->centroid);
	//$kmeans->SaveProcess($n,($endTime-$startTime));
=======
>>>>>>> 3b7feb4fcdbab6c9b405b9a03a9afac1b8c1a2b3


//route for trying anything
Route::get('coba', function()
{
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
	 $timer = new TimeExecution;
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

	 $phisum = array();
	 $phisum[0] = array(1,2,3);
	 $phisum[1] = array(1,1,1);
	 //var_dump($phisum);

	 $nw = array();
	 $nw[0] = array(1,2);
	 $nw[1] = array(3,1);
	 $nw[2] = array(2,1);
	 //var_dump($nw);

	 $nwsum = array(5,10);

	 // $count = 2;
	 $arr = array();
	 for ($i=0; $i <2 ; $i++) { 
	 	$arr[$i] = array();
	 	for ($j=0; $j <3 ; $j++) { 
	 		$arr[$i][$j] = $phisum[$i][$j];
	 		//$arr[$i][$j] = $nw[$j][$i]+$nwsum[$i];
	 	}
	 }
	 //var_dump($arr);

	 $row = count($arr);
	 $col = count($arr[0]);
	 $rra = array();
	 for ($x=0; $x < $col; $x++) { 
	 	$rra[$x] =array();
	 	for ($y=0; $y < $row; $y++) { 
	 		$rra[$x][$y] = $arr[$y][$x];
	 	}
	 }
	 var_dump($rra);

	 // $count2 = 2;
	 $arrx = array();
	 for ($j=0; $j <3 ; $j++) { 
	 	$arrx[$j]= array();
	 	for ($i=0; $i <2 ; $i++) { 	
	 		//$arrx[$j][$i] = $nw[$j][$i]+$nwsum[$i];
	 		//echo "[".$j."][".$i."] = ".$nw[$j][$i]."+".$nwsum[$i]."<br />";
	 		$arrx[$j][$i] = $phisum[$i][$j];
	 	}
	 }
	 var_dump($arrx);
 });


