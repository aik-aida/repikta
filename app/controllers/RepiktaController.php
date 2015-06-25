<?php

	class RepiktaController extends BaseController{

		public function read_transkrip(){
			
			$filename = Input::file('file')->getClientOriginalName();
			$pathfile = './data/';
			Input::file('file')->move($pathfile, $filename);
			$file = $pathfile.$filename;
			$arr_kode_mk = array();
			$transkrip_mhs = (object) array();

			$data_transkrip = (object) array (	"status",
												"nrp",
												"nama",
												"mkpersiapan" => array(),
												"mksarjana" => array(),
												"totalsks",
												"ipk",
												"tanggal"
												);
			$no=1;
			$tahap1=0;
			$tahap1_1=0;
			$tahap1_2=0;
			$tahap1_3=0;
			$tahap1_4=0;
			$tahap1_5=0;
			$tahap2=0;
			$tahap2_1=0;
			$tahap2_2=0;
			$tahap2_3=0;
			$tahap2_4=0;
			$tahap2_5=0;
			$dont=false;
			$sks=0;
			$ipk=0;
			$persiapan=0;
			$sarjana=0;

			$html = new Htmldom($file);
			$data = $html->find('table td');
			
			if (strcmp(strtolower($data[0]->plaintext), strtolower("TRANSKRIP MATA KULIAH"))==0) {
			    $data_transkrip->status = 'Transkrip Diterima';
			    
			    $mk = (object) array(	"kode",
										"nama",
										"sks",
										"catatan",
										"nilai");
			    foreach( $data as $element){
			    	
			    	$temp=explode(" : ", $element->plaintext);

			    	if(strcmp(strtolower($element->plaintext), strtolower("--- Tahap: Persiapan ---"))==0){
			    			$tahap1=$no;
			    			$tahap1_1=($tahap1+1)%5;
			    			$tahap1_2=($tahap1+2)%5;
			    			$tahap1_3=($tahap1+3)%5;
			    			$tahap1_4=($tahap1+4)%5;
			    			$tahap1_5=($tahap1+5)%5;
			    	}
			    	if(strcmp(strtolower($element->plaintext), strtolower("--- Tahap: Sarjana ---"))==0){
			    			$tahap2=$no;
			    			$tahap2_1=($tahap2+1)%5;
			    			$tahap2_2=($tahap2+2)%5;
			    			$tahap2_3=($tahap2+3)%5;
			    			$tahap2_4=($tahap2+4)%5;
			    			$tahap2_5=($tahap2+5)%5;	
			    	}

			    	if(strcmp(strtolower($temp[0]), strtolower("Total Sks Tahap Persiapan"))==0 ||
			    		strcmp(strtolower($temp[0]), strtolower("IP Tahap Persiapan"))==0 ||
			    		strcmp(strtolower($temp[0]), strtolower("Total Sks Tahap Sarjana"))==0 ||
			    		strcmp(strtolower($temp[0]), strtolower("IP Tahap Sarjana"))==0 ){
			    		$dont=true;
			    	}
			    	
					if(strcmp(strtolower($element->plaintext), strtolower("Total Sks"))==0){
			    		$sks=$data[$no]->plaintext;
			    		$dont=true;
			    	}
			    	elseif(strcmp(strtolower($element->plaintext), strtolower("IPK"))==0) {
			    		$ipk=$data[$no]->plaintext;
			    		$dont=true;
			    	}


			    	if($no==3) {
			    			$id = explode("/", $element->plaintext);
			    			$data_transkrip->nrp = $id[0];
			    			$data_transkrip->nama = str_replace('<', '', $id[1]);
			    			
			    	}
			    	elseif ($no>($tahap1+5) && $tahap2==0 && $dont==false && $ipk==0 && $sks==0) {
			    			switch ($no%5) {
			    				case $tahap1_1:
			    					$data_transkrip->mkpersiapan[$persiapan]=(object) array("kode"=>$element->plaintext);
			    					//$mk->kode = $element;
			    					break;
			    				case $tahap1_2:
			    					//array_push($data_transkrip->mkpersiapan[$persiapan], var)
			    					//$mk->nama = $element;
			    					$data_transkrip->mkpersiapan[$persiapan]->nama=$element->plaintext;
			    					break;
			    				case $tahap1_3:
			    					//$mk->sks = $element;
			    					$data_transkrip->mkpersiapan[$persiapan]->sks=$element->plaintext;
			    					break;
			    				case $tahap1_4:
			    					$data_transkrip->mkpersiapan[$persiapan]->catatan=$element->plaintext;
			    					break;
			    				case $tahap1_5:
			    					//$mk->nilai = $element;
			    					$data_transkrip->mkpersiapan[$persiapan]->nilai=$element->plaintext;
			    					$persiapan++;
			    					//array_push($data_transkrip->mkpersiapan, $mk);

			    					break;
			    				default:
			    					break;
			    			}
			    	}
			    	elseif ($no>($tahap2+5) && $tahap2>$tahap1 && $dont==false && $ipk==0 && $sks==0) {
			    		switch ($no%5) {
			    				case $tahap2_1:
			    					$data_transkrip->mksarjana[$sarjana]=(object) array("kode"=>$element->plaintext);
			    					break;
			    				case $tahap2_2:
			    					$data_transkrip->mksarjana[$sarjana]->nama=$element->plaintext;
			    					break;
			    				case $tahap2_3:
			    					$data_transkrip->mksarjana[$sarjana]->sks=$element->plaintext;
			    					break;
			    				case $tahap2_4:
			    					$data_transkrip->mksarjana[$sarjana]->catatan=$element->plaintext;
			    					break;
			    				case $tahap2_5:
			    					$data_transkrip->mksarjana[$sarjana]->nilai=$element->plaintext;
			    					$sarjana++;
			    					break;
			    				default:
			    					break;
			    			}
			    	}
			    	
			    	$no++;
			    	$dont=false;
			    }

			    $tgl=explode(": ", $data[count($data)-1]);
			    $tgl_transkrip=$tgl[1];
			    $data_transkrip->totalsks = $sks;
			    $data_transkrip->ipk = $ipk;
			    $data_transkrip->tanggal = $tgl_transkrip;
			}
			else {
			    $data_transkrip->status = 'File yang Anda Masukan Salah !!!<br/>';
			}

			foreach ($data_transkrip->mkpersiapan as $key => $data) {
				$kode = $data->kode;
				$awal = substr($kode, 0,2);
				$akhir = substr($kode, -4);
				$tahun = substr($kode, 2, 3);
				if($awal=='KI'){
					$kode_mk=null;

					if($tahun=='14'){
						$mk_ekui = dbEkuivalensi::find($kode);
						if($mk_ekui!=null){
							$kode_mk = $mk_ekui->kode_mk_2009;
							// echo $kode."-".$kode_mk."<br />";
							// echo "ekui<br />";
						}
					}elseif($tahun=='09'){
						$kode_mk = $awal.$akhir;
						// echo $kode."-".$kode_mk."<br />";
						// echo "tetep<br />";
					}
					
					if($kode_mk!=null){
						$numerik = $this->get_numerik($data->nilai);
						// echo "--persiapan ".$kode_mk."<br />";
						array_push($arr_kode_mk, $kode_mk);
						$transkrip_mhs->$kode_mk = $numerik;
					}
				}
			}
			foreach ($data_transkrip->mksarjana as $key => $data) {
				$kode = $data->kode;
				$awal = substr($kode, 0,2);
				$akhir = substr($kode, -4);
				$tahun = substr($kode, 2, 2);
				if($awal=='KI'){
					$kode_mk=null;

					if($tahun=='14'){
						$mk_ekui = dbEkuivalensi::find($kode);
						if($mk_ekui!=null){
							$kode_mk = $mk_ekui->kode_mk_2009;
							// echo $kode."-".$kode_mk."<br />";
							// echo "ekui<br />";
						}
					}elseif($tahun=='09'){
						$kode_mk = $awal.$akhir;
						// echo $kode."-".$kode_mk."<br />";
						// echo "tetep<br />";
					}
					
					if($kode_mk!=null){
						$numerik = $this->get_numerik($data->nilai);
						// echo "--sarjana ".$kode_mk."<br />";
						array_push($arr_kode_mk, $kode_mk);
						$transkrip_mhs->$kode_mk = $numerik;
					}
				}
			}

			$json_transkrip = (object) array();
			$mk_all = dbMataKuliah::all();
			foreach ($mk_all as $key => $dt) {
				$kode = $dt->mk_kode;
				if(in_array($kode, $arr_kode_mk)){
					$json_transkrip->$kode = $transkrip_mhs->$kode;
				}
				else{
					$json_transkrip->$kode = 0;
				}
			}

			//var_dump($arr_kode_mk);
	
			return View::make('transkrip')
            	->with('data', $data_transkrip)
            	->with('transkrip_mhs', json_encode($json_transkrip));

			// return View::make('showTranskrip')
			// 	->with('data', $data_transkrip);
		}

		public function get_numerik($huruf)
		{

			$angka = null;
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
			
			return $angka;
		}

		public function rekomendasi()
		{
			$datanrp = Input::only(['nrp']);
			$nrp = $datanrp['nrp'];

			$datanama = Input::only(['nama']);
			$nama = $datanama['nama'];

			$datatranskrip = Input::only(['transkrip']);
			$transkrip_masukan = json_decode($datatranskrip['transkrip']);

			$current_use = dbCurrentUse::all();
			$idgroup_result = $current_use[0]->id_kluster;
			$id_hasil_lda = $current_use[0]->id_lda;
			$n = 5;
			$nshow = 20;
			$repikta = new Repikta;

			//MENCARI KLUSTER PILIHAN
			$kluster = $repikta->Choose_Kluster($transkrip_masukan,$idgroup_result);
			$nama_bidang = $repikta->GetKlusterName($kluster);

			//MENDAPATKAN LDA TOPIK PADA KLUSTER TERPILIH
			$lda_result = dbLdaSave::where('percobaan_ke','=',$id_hasil_lda)
								->where('kluster_ke','=',$kluster)->get();

			$list_nrp = json_decode($lda_result[0]->daftar_dokumen);		//DAFTAR DOKUMEN PADA KLUSTER TERPILIH
			$theta_matrix = json_decode($lda_result[0]->matriks_theta);		//THETA LDA PADA KLUSTER TERPILIH
			$phi_matrix = json_decode($lda_result[0]->matriks_phi);			//PHI LDA PADA KLUSTER TERPILIH
			$k = $lda_result[0]->k_topik;									//JUMLAH TOPIK PADA KLUSTER TERPILIH
			$nterm = $lda_result[0]->n_term;								//BANYAK TERM LDA PADA KLUSTER TERPILIH
			$list_term = json_decode($lda_result[0]->matriks_term);			//DAFTAR TERM LDA PADA KLUSTER TERPILIH

			//LIST TERM TIAP TOPIK YANG AKAN DITAMPILKAN
			$topic = $repikta->Get20TermTopic($k, $phi_matrix, $nterm, $list_term); 

			//MENCARI n DOKUMEN TERDEKAT PADA KLUSTER TERPILIH
			$nrp_terdekat_kluster = $repikta->GetClosestDoc($transkrip_masukan, $idgroup_result, $kluster,$n);

			$topik_terdekat = array();		//DAFTAR TOPIK n DOKUMEN TERDEKAT URUT BERDASARKAN DOKUMEN TERDEKAT
			$kemunculan_topik = array();	//JUMLAH KEMUNCULAN MASING-MASING TOPIK DALAM n DOKUMEN TERDEKAT
			
			for ($i=0; $i <$k ; $i++) { 
				$kemunculan_topik[$i]=0;
			}
			foreach ($nrp_terdekat_kluster as $key => $value) {
				$index_nrp = array_search($value, $list_nrp);		//mendapatkan id dokumen terdepat dalam matriks
				$vtopic = $theta_matrix[$index_nrp];				//daftar probabilitas topik dokumen
				$idtopic = array_search(max($vtopic), $vtopic);		//mendapatkan topik terpilih dokumen
				array_push($topik_terdekat, $idtopic);
				$kemunculan_topik[$idtopic]++;
			}

			//MENGURUTKAN TOPIK BERDASAR KEMUNCULAN TERBANYAK DARI n DOKUMEN
			arsort($kemunculan_topik);

			$idtopic_muncul = array();
			$jumlah_muncul = array();
			foreach ($kemunculan_topik as $key => $value) {
				array_push($idtopic_muncul, $key);
				array_push($jumlah_muncul, $value);
			}

			//$dokumen_detail = dbDokumen::find($nrp);
			$dokumen_detail = (object) array();
			$dokumen_detail->nama = $nama;
			$dokumen_detail->nrp = $nrp;

			return View::make('rekomendasi')
					->with('data', $dokumen_detail)
					->with('bidang', $nama_bidang)
					->with('muncul_topik', $jumlah_muncul)
					->with('idmuncul', $idtopic_muncul)
					->with('nshow', $nshow)
					->with('topic', $topic)
					->with('ktopik', $k)
					->with('n', $n)
					->with('id_klp', $kluster);
		}

		public function rekomendasi_katadokumen($param, $klp)
		{
			$datakata = dbKamusKata::find($param);
			$list_doc = json_decode($datakata->indoc);
			$data_doc = array();

			$current_use = dbCurrentUse::all();
			$id_group = $current_use[0]->id_kluster;
			$id_result = DB::table('kmeans_result')->where('id_group', '=' , $id_group)->max('id');
			$data_result = dbKmeansResult::find($id_result);
			$hasil_kluster = json_decode($data_result->hasil_kluster);
			$in_klp = array();
			$in_klp = $hasil_kluster[$klp];

			foreach ($list_doc as $key => $nrp) {
				if(in_array($nrp, $in_klp)){
					$doc = dbDokumen::find($nrp);
					array_push($data_doc, $doc);
				}
			}

			return View::make('rekomendasi_dokumen')
						->with('term', $param)
						->with('daftar_doc', $data_doc);
		}

		public function rekomendasi_dokumen()
		{
			$data = Input::only(['iddoc']);
			$id = $data['iddoc'];
			$dataterm = Input::only(['term']);
			$term = $dataterm['term'];

			$dokumen_detail = dbDokumen::find($id);
			return View::make('rekomendasi_dokumen_detail')
					->with('dokumen', $dokumen_detail)
					->with('term', $term);
		}


		public function show_phi(){
			$id_lda = 44;
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
					echo $key."<br />"; $i++;
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