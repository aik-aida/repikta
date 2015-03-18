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
	return View::make('hello');
});

//get TA data (sw 110 111)
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

Route::get('preprocessing', function()
{
	$dokumens = Dokumen::all();
	$count=0;
	foreach ($dokumens as $key => $value) {
		$teks = (object) array("input","afstemming","afremoval");
		$teks->input = $value->abstraksi_ta;

		$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
		$stemmer  = $stemmerFactory->createStemmer();
		$teks->afstemming = $stemmer->stem($teks->input);

		$stopwordRemoval= new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
		$removal  = $stopwordRemoval->createStopWordRemover();
		$teks->afremoval = $removal->remove($teks->afstemming);

		$update_preprocessing = Dokumen::find($value->nrp);
		$update_preprocessing->abstrak_af_preproc = $teks->afremoval;
		$update_preprocessing->save();
		$count++;
		echo "preprocessing".$count."<br />";	
	}
});

Route::get('coba', function()
{
	//get substring
	echo substr("5110100020", 2, 2);

	//see json data
	$data = array('nama'	=> 'aida',
				  'nrp'		=> '5111100020',
				  'jk'		=> 'perempuan');
	echo json_encode($data);
	echo "<br /> <br />";

	$dt = '{"nama":"aida","nrp":"5111100020","jk":"perempuan"}';
	$arr = json_decode($dt);
	echo $arr->nama."<br />";
	echo $arr->nrp."<br />";
	echo $arr->jk."<br />";
});

