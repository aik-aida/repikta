<?php
	/**
	* 
	*/
	class AdminController extends BaseController
	{
		public function dashboard()
		{
			$current_use = dbCurrentUse::all();
			$idgroup_result = $current_use[0]->id_kluster;
			$id_hasil_lda = $current_use[0]->id_lda;

			$idresult = DB::table('kmeans_result')->where('id_group', '=' , $idgroup_result)->max('id');
			$result = DB::table('kmeans_result')->where('id', '=' , $idresult)->get();
			$kelompok = json_decode($result[0]->hasil_kluster);

			

			$main = new Repikta;
			$banyak_bidang = count($kelompok);
			$nama_bidang = array();
			$banyak_topik_bidang = array();

			for ($i=0; $i <$banyak_bidang ; $i++) { 
				array_push($nama_bidang, $main->GetKlusterName($i));
				$lda_result = dbLdaSave::where('percobaan_ke','=',$id_hasil_lda)
								->where('kluster_ke','=',$i)->get();
				array_push($banyak_topik_bidang, $lda_result[0]->k_topik);
			}

			$nama_topik_bidang = array();
			for ($i=0; $i <$banyak_bidang ; $i++) { 
				$nama_topik = array();
				for ($j=0; $j < $banyak_topik_bidang[$i]; $j++) { 
					$topic_name = dbNamaTopik::where('id_percobaan_lda','=',$id_hasil_lda)
											->where('id_kelompok','=',$i)
											->where('id_topik','=',$j)
											->get();
					//array_push($nama_topik, ('Topik '.($j+1)));
					array_push($nama_topik, ($topic_name[0]->nama_topik));
				}
				array_push($nama_topik_bidang, $nama_topik);
			}

			return View::make('dashboard')
						->with('n_bidang', $banyak_bidang)
						->with('nama_bidang', $nama_bidang)
						->with('nama_topik_bidang', $nama_topik_bidang);
		}

		public function dashboard_dokummen()
		{

			$current_use = dbCurrentUse::all();
			$idgroup_result = $current_use[0]->id_kluster;
			$id_hasil_lda = $current_use[0]->id_lda;

			$idresult = DB::table('kmeans_result')->where('id_group', '=' , $idgroup_result)->max('id');
			$data_last = DB::table('kmeans_result')->where('id', '=' , $idresult)->get();

			$hasil_kluster = json_decode($data_last[0]->hasil_kluster);
			$main = new Repikta;
			for ($i=0; $i < count($hasil_kluster); $i++) { 
				$penamaan[$i]['nama'] = $main->GetKlusterName($i);
				$penamaan[$i]['kode'] = 'kelompok'.($i+1);
				$penamaan[$i]['href'] = '#kelompok'.($i+1);
				$penamaan[$i]['file'] = array();
				foreach ($hasil_kluster[$i] as $key => $iddoc) {
					$dokumen = DB::table('dokumen')->select('judul_ta')->where('nrp', '=' , $iddoc)->get();
					array_push($penamaan[$i]['file'], $dokumen[0]);
				}
			}

			return View::make('dashboard_dokumen_detail')
					->with('datamain', $data_last[0])
					->with('datakluster', $penamaan);
		}

		public function dashboard_transkrip()
		{
			$data = Input::only(['id_klaster']);
			$id = $data['id_klaster'];

			$current_use = dbCurrentUse::all();
			$idgroup_result = $current_use[0]->id_kluster;
			$id_hasil_lda = $current_use[0]->id_lda;

			$main = new Repikta;
			$nama_bidang = $main->GetKlusterName($id);

			$dt_transkrip = dbTranskripKriteria::where('group','=',$idgroup_result)->get();
			$all_transkrip = json_decode($dt_transkrip[0]->kriteria_transkrip);
			$transkrip = $all_transkrip[$id];
			//$transkrip = json_decode($dokumen_detail->transkrip);
			$mk_umum = DB::table('matakuliah')->where('mk_bidang_keahlian', '=' , false)->get();
			$mk_ahli = DB::table('matakuliah')->where('mk_bidang_keahlian', '=' , true)->get();
			$arr_kode_mk_umum = array();
			$arr_kode_mk_ahli = array();
			$nilai_umum = array();
			$nilai_ahli = array();

			foreach ($mk_umum as $key => $mk) {
				array_push($arr_kode_mk_umum, $mk->mk_kode);
			}
			foreach ($mk_ahli as $key => $mk) {
				array_push($arr_kode_mk_ahli, $mk->mk_kode);
			}

			// var_dump($transkrip);


			foreach ($transkrip as $key => $nilai) {
				if($nilai != 0){
					if($nilai>3.5){
						$huruf = 'A';
					}elseif ($nilai>3 && $nilai<=3.5) {
						$huruf = 'AB';
					}elseif ($nilai>2.5 && $nilai<=3) {
						$huruf = 'B';
					}elseif ($nilai>2 && $nilai<=2.5) {
						$huruf = 'BC';
					}elseif ($nilai>1 && $nilai<=2) {
						$huruf = 'C';
					}elseif ($nilai>0 && $nilai<=1) {
						$huruf = 'D';
					}
					
					$mk_detail = dbMataKuliah::find($key);
					$add = (object) array();
					$add->kode = $mk_detail->mk_kode;
					$add->nama = $mk_detail->mk_nama;
					$add->nilai = $huruf;

					if(in_array($key, $arr_kode_mk_umum)){
						array_push($nilai_umum, $add);
					}
					elseif(in_array($key, $arr_kode_mk_ahli)){
						array_push($nilai_ahli, $add);
					}
				}
			}

			return View::make('dashboard_transkrip')
						->with('nama_bidang', $nama_bidang)
						->with('mk_umum', $nilai_umum)
						->with('mk_ahli', $nilai_ahli);
		}

		public function dashboard_topik()
		{
			$data = Input::only(['id_klaster']);
			$id = $data['id_klaster'];

			$current_use = dbCurrentUse::all();
			$idgroup_result = $current_use[0]->id_kluster;
			$id_hasil_lda = $current_use[0]->id_lda;

			$main = new Repikta;
			$nama_bidang = $main->GetKlusterName($id);
			$lda_result = dbLdaSave::where('percobaan_ke','=',$id_hasil_lda)
								->where('kluster_ke','=',$id)->get();

			$list_nrp = json_decode($lda_result[0]->daftar_dokumen);		//DAFTAR DOKUMEN PADA KLUSTER TERPILIH
			$theta_matrix = json_decode($lda_result[0]->matriks_theta);		//THETA LDA PADA KLUSTER TERPILIH
			$phi_matrix = json_decode($lda_result[0]->matriks_phi);			//PHI LDA PADA KLUSTER TERPILIH
			$k = $lda_result[0]->k_topik;									//JUMLAH TOPIK PADA KLUSTER TERPILIH
			$nterm = $lda_result[0]->n_term;								//BANYAK TERM LDA PADA KLUSTER TERPILIH
			$list_term = json_decode($lda_result[0]->matriks_term);			//DAFTAR TERM LDA PADA KLUSTER TERPILIH
			$nama_topik = array();
			$topic = $main->Get20TermTopic($k, $phi_matrix, $nterm, $list_term);
			for ($i=0; $i <$k ; $i++) { 
				$topic_name = dbNamaTopik::where('id_percobaan_lda','=',$id_hasil_lda)
											->where('id_kelompok','=',$id)
											->where('id_topik','=',$i)
											->get();
				array_push($nama_topik, $topic_name[0]->nama_topik);
			}
			
			return View::make('dashboard_topic')
						->with('nama_bidang', $nama_bidang)
						->with('ktopik', $k)
						->with('list_topic', $topic)
						->with('nama_topik', $nama_topik)
						->with('id_klp', $id);
		}

		public function kamus_list(){
			$kamus = dbKamusKata::get();
			return View::make('kamus_main')
					->with('kamus', $kamus);
		}

		public function dokumen_list(){
			$corpus = dbDokumen::where('training','=',true)->get();
			return View::make('dokumen_main')
					->with('corpus', $corpus);
		}

		public function dokumen_detail(){
			$data = Input::only(['iddoc']);
			$id = $data['iddoc'];

			$dokumen_detail = dbDokumen::find($id);

			return View::make('dokumen_detail')
					->with('dokumen', $dokumen_detail);
		}

		public function dokumen_tf(){
			$data = Input::only(['iddoc']);
			$id = $data['iddoc'];
			$dokumen_detail = dbDokumen::find($id);

			$vectortfjudul = json_decode($dokumen_detail->nilai_tf_judul);
			$vectortfabstrak = json_decode($dokumen_detail->nilai_tf_abstrak);
			$sort_judul = array();
			$sort_abstrak = array();
			foreach ($vectortfjudul as $key => $value) {
				$sort_judul[$key] = $value;
			}
			foreach ($vectortfabstrak as $key => $value) {
				$sort_abstrak[$key] = $value;
			}
			arsort($sort_judul); arsort($sort_abstrak);
			$urut_judul = array_keys($sort_judul); $urut_abstrak = array_keys($sort_abstrak);

			return View::make('dokumen_tf')
					->with('id', $id)
					->with('kamus_abstrak', $urut_abstrak)
					->with('kamus_judul', $urut_judul)
					->with('vectortfjudul', $sort_judul)
					->with('vectortfabstrak', $sort_abstrak);
		}

		public function dokumen_tfidf(){
			$data = Input::only(['iddoc']);
			$id = $data['iddoc'];

			$kamus = dbKamusKata::get();
			$dokumen_detail = dbDokumen::find($id);
			$vectortfidf = json_decode($dokumen_detail->nilai_tfidf);
			
			$sort_tfidf = array();
			foreach ($vectortfidf as $key => $value) {
				$sort_tfidf[$key] = $value;
			}
			arsort($sort_tfidf);
			$uruterm = array_keys($sort_tfidf);

			return View::make('dokumen_tfidf')
					->with('id', $id)
					->with('kamus', $kamus)
					->with('vectortfidf', $vectortfidf)
					->with('urut', $sort_tfidf)
					->with('kata', $uruterm);
		}

		public function centroid_list(){
			// $gencen = CentroidGenerated::get();
			// return View::make('centroid_main')
			// 		->with('centroids', $gencen);
		}

		public function centroid_detail(){
			// $data = Input::only(['idcentroid']);
			// $id = $data['idcentroid'];
			// //echo $id;
			// $kamus = KamusKata::get();
			// $centroid_obj = CentroidGenerated::find($id);
			// $centroid = json_decode($centroid_obj->centroid);
			// $corpus = json_decode($centroid_obj->dokumen);
			// // echo count($centroid_obj)."<br />";
			// // var_dump($centroid);

			// return View::make('centroid_detail')
			// 		->with('id', $id)
			// 		->with('data', $centroid)
			// 		->with('corpus', $corpus)
			// 		->with('kamus', $kamus);
		}

		public function kluster_list(){
			
			$jumlah_iterasi = array();
			$total_waktu = array();
			$kluster = array();
			$id_first = array();


			$data = dbKmeansResult::get();

			$id_kluster = DB::table('kmeans_result')->select('id_group')->distinct()->get();

			foreach ($id_kluster as $key => $dt) {
				$id = DB::table('kmeans_result')->where('id_group', '=' , $dt->id_group)->min('id');
				$kls = DB::table('kmeans_result')->where('id', '=' , $id)->get();
				$jumlah = DB::table('kmeans_result')->where('id_group', '=' , $dt->id_group)->count();
				$dum_kluster = DB::table('kmeans_result')->where('id_group', '=' , $dt->id_group)->get();

				$sum = 0.0;
				foreach ($dum_kluster as $key => $dataone) {
					$sum += $dataone->lama_eksekusi;
				}

				array_push($total_waktu, $sum);
				array_push($kluster, $kls[0]);
				array_push($jumlah_iterasi, $jumlah);
				array_push($id_first, $id);
			}

			$n = count($id_kluster);

			return View::make('kmeans_main')
					->with('jumlah', $n)
					->with('data', $kluster)
					->with('niterasi', $jumlah_iterasi)
					->with('nwaktu', $total_waktu)
					->with('iddata', $id_first);
		}

		public function kluster_detail(){
			$data = Input::only(['idkluster']);
			$id = $data['idkluster'];
			$penamaan = array();

			$data_first = DB::table('kmeans_result')->where('id', '=' , $id)->get();
			$detail_iterasi = DB::table('kmeans_result')->where('id_group', '=' , $data_first[0]->id_group)->get();
			$idresult = DB::table('kmeans_result')->where('id_group', '=' , $data_first[0]->id_group)->max('id');
			$data_last = DB::table('kmeans_result')->where('id', '=' , $idresult)->get();

			$sum = 0.0;
			foreach ($detail_iterasi as $key => $dt) {
				$sum += $dt->lama_eksekusi;
			}


			$hasil_kluster = json_decode($data_last[0]->hasil_kluster);

			for ($i=0; $i < count($hasil_kluster); $i++) { 
				$penamaan[$i]['nama'] = 'KLUSTER '.($i+1);
				$penamaan[$i]['kode'] = 'kluster'.($i+1);
				$penamaan[$i]['href'] = '#kluster'.($i+1);
				$penamaan[$i]['file'] = array();
				foreach ($hasil_kluster[$i] as $key => $iddoc) {
					$dokumen = DB::table('dokumen')->select('nrp', 'nama', 'judul_ta', 'rmk')->where('nrp', '=' , $iddoc)->get();
					array_push($penamaan[$i]['file'], $dokumen[0]);
				}
			}

			return View::make('kmeans_detail')
					->with('datamain', $data_first[0])
					->with('niterasi', count($detail_iterasi))
					->with('nlama', $sum)
					->with('hasilkluster', $hasil_kluster)
					->with('datakluster', $penamaan);
		}

		public function testing_list()
		{
			$dokumens = dbDokumen::where('training','=',false)->get();
			return View::make('testing_dokumen')
					->with('data', $dokumens);
		}

		public function testing_transkrip()
		{
			$data = Input::only(['nrp']);
			$id = $data['nrp'];

			$dokumen_detail = dbDokumen::find($id);
			$transkrip = json_decode($dokumen_detail->transkrip);
			$mk_umum = DB::table('matakuliah')->where('mk_bidang_keahlian', '=' , false)->get();
			$mk_ahli = DB::table('matakuliah')->where('mk_bidang_keahlian', '=' , true)->get();
			$arr_kode_mk_umum = array();
			$arr_kode_mk_ahli = array();
			$nilai_umum = array();
			$nilai_ahli = array();

			foreach ($mk_umum as $key => $mk) {
				array_push($arr_kode_mk_umum, $mk->mk_kode);
			}
			foreach ($mk_ahli as $key => $mk) {
				array_push($arr_kode_mk_ahli, $mk->mk_kode);
			}

			foreach ($transkrip as $key => $nilai) {
				if($nilai != 0){
					switch ($nilai) {
						case 4:
							$huruf = 'A';
							break;
						case 3.5:
							$huruf = 'AB';
							break;
						case 3:
							$huruf = 'B';
							break;
						case 2.5:
							$huruf = 'BC';
							break;
						case 2:
							$huruf = 'C';
							break;
						case 1:
							$huruf = 'D';
							break;
						case 0:
							$huruf = 'E';
							break;
					}

					$mk_detail = dbMataKuliah::find($key);
					$add = (object) array();
					$add->kode = $mk_detail->mk_kode;
					$add->nama = $mk_detail->mk_nama;
					$add->nilai = $huruf;

					if(in_array($key, $arr_kode_mk_umum)){
						array_push($nilai_umum, $add);
					}
					elseif(in_array($key, $arr_kode_mk_ahli)){
						array_push($nilai_ahli, $add);
					}
				}
			}


			return View::make('testing_transkrip')
					->with('data', $dokumen_detail)
					->with('mk_umum', $nilai_umum)
					->with('mk_ahli', $nilai_ahli);
			
		}

		public function testing_rekomendasi()
		{
			$data = Input::only(['nrp']);
			$nrp = $data['nrp'];

			$current_use = dbCurrentUse::all();
			$idgroup_result = $current_use[0]->id_kluster;
			$id_hasil_lda = $current_use[0]->id_lda;
			$n = 5;
			$nshow = 20;
			$repikta = new Repikta;

			//MENCARI KLUSTER PILIHAN
			$mhs = dbDokumen::find($nrp);
			$mhs_transkrip = json_decode($mhs->transkrip);
			$kluster = $repikta->Choose_Kluster($mhs_transkrip,$idgroup_result);
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
			$nrp_terdekat_kluster = $repikta->GetClosest($nrp, $kluster,$n);

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

			$dokumen_detail = dbDokumen::find($nrp);

			return View::make('testing_rekomendasi')
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

		public function testing_rekomendasi_katadokumen($param, $klp)
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

			return View::make('testing_rekomendasi_dokumen')
						->with('term', $param)
						->with('daftar_doc', $data_doc);
		}

		public function testing_rekomendasi_dokumen()
		{
			$data = Input::only(['iddoc']);
			$id = $data['iddoc'];
			$dataterm = Input::only(['term']);
			$term = $dataterm['term'];

			$dokumen_detail = dbDokumen::find($id);
			return View::make('testing_rekomendasi_dokumen_detail')
					->with('dokumen', $dokumen_detail)
					->with('term', $term);
		}

		public function survey_login()
		{
			return View::make('survey_login');
		}

		public function survey_penjelasan()
		{
			$data_nrp = Input::only(['nrp']);
			$nrp = $data_nrp['nrp'];

			$data_nama = Input::only(['nama']);
			$nama = $data_nama['nama'];

			$id_survey = 1;
			$survey = dbSurveyDaftar::find($id_survey);
			$docs = json_decode($survey->dokumen_survey);

			$now = 0;
			$all = count($docs);

			
			return View::make('survey_penjelasan')
					->with('survey', $id_survey)
					->with('nrp', $nrp)
					->with('nama', $nama)
					->with('now', $now)
					->with('all', $all);
		}

		public function survey_dokumen()
		{
			$data_nrp = Input::only(['nrp']);
			$nrp_user = $data_nrp['nrp'];

			$data_nama = Input::only(['nama']);
			$nama_user = $data_nama['nama'];

			$data_idx = Input::only(['number']);
			$idx = $data_idx['number'];

			$data_survey = Input::only(['survey']);
			$id_survey = $data_survey['survey'];

			if($idx>0){
				$data_nilai = Input::only(['nilai']);
				$nilai = $data_nilai['nilai'];

				$data_dokumen = Input::only(['dokumen']);
				$dokumen = $data_dokumen['dokumen'];

				$simpan_nilai = new dbSurveyData;
				$simpan_nilai->id_survey = $id_survey;
				$simpan_nilai->nrp_penguji = $nrp_user;
				$simpan_nilai->dokumen = $dokumen;
				$simpan_nilai->nilai = $nilai;
				$simpan_nilai->save();
			}

			$current_use = dbCurrentUse::all();
			$idgroup_result = $current_use[0]->id_kluster;
			$id_hasil_lda = $current_use[0]->id_lda;
			
			$survey = dbSurveyDaftar::find($id_survey);
			$docs = json_decode($survey->dokumen_survey);
			$dokumen = dbDokumen::find($docs[$idx]);

			$data = dbTestingRekomendasi::where('nrp_testing','=',$dokumen->nrp)
										->where('id_kmeans','=',$idgroup_result)
										->where('id_lda','=',$id_hasil_lda)
										->get();

			$kluster = $data[0]->kluster_bidang;
			$get_nama_topic = dbNamaTopik::where('id_percobaan_lda','=',$id_hasil_lda)
										->where('id_kelompok','=',$kluster)
										->orderBy('id_topik','asc')->get();
			$kemunculan_topik = json_decode($data[0]->kemunculan_topik);

			$n = 5;
			$nshow = 20;
			$repikta = new Repikta;

			//MENDAPATKAN LDA TOPIK PADA KLUSTER TERPILIH
			$lda_result = dbLdaSave::where('percobaan_ke','=',$id_hasil_lda)
								->where('kluster_ke','=',$kluster)->get();

			$list_nrp = json_decode($lda_result[0]->daftar_dokumen);		//DAFTAR DOKUMEN PADA KLUSTER TERPILIH
			$theta_matrix = json_decode($lda_result[0]->matriks_theta);		//THETA LDA PADA KLUSTER TERPILIH
			$phi_matrix = json_decode($lda_result[0]->matriks_phi);			//PHI LDA PADA KLUSTER TERPILIH
			$k = $lda_result[0]->k_topik;									//JUMLAH TOPIK PADA KLUSTER TERPILIH
			$nterm = $lda_result[0]->n_term;								//BANYAK TERM LDA PADA KLUSTER TERPILIH
			$list_term = json_decode($lda_result[0]->matriks_term);			//DAFTAR TERM LDA PADA KLUSTER TERPILIH

			//MENCARI KLUSTER PILIHAN
			$nama_bidang = $repikta->GetKlusterName($kluster);

			//LIST TERM TIAP TOPIK YANG AKAN DITAMPILKAN
			$topic = $repikta->Get20TermTopic($k, $phi_matrix, $nterm, $list_term);

			$idtopic_muncul = array();
			$jumlah_muncul = array();
			foreach ($kemunculan_topik as $key => $value) {
				array_push($idtopic_muncul, $key);
				array_push($jumlah_muncul, $value);
			}

			$now = $idx+1;
			$all = count($docs);

			$daftar_nama_topic = array();
			$daftar_topic = array();
			$daftar_bobot = array();

			for ($i = 0; $i < $k; $i++){
                if($jumlah_muncul[$i]!=0) {
                	array_push($daftar_nama_topic, $get_nama_topic[$idtopic_muncul[$i]]->nama_topik);
                	array_push($daftar_bobot,  (($jumlah_muncul[$i]/$n)*100) ); //bobot
                	$kata_topik = array();
					for ($x = 0; $x < $nshow; $x++){
						array_push($kata_topik, $topic[$idtopic_muncul[$i]][$x] );  //kata
					}
					array_push($daftar_topic, $kata_topik);
				}
			}
			$ntopik = count($daftar_bobot);
			

			return View::make('survey_dokumen')
					->with('survey', $id_survey)
					->with('dokumen', $dokumen)
					->with('nrp', $nrp_user)
					->with('nama', $nama_user)
					->with('now', $now)
					->with('all', $all)
					->with('bidang', $nama_bidang)
					->with('daftar', $daftar_topic)
					->with('bobot', $daftar_bobot)
					->with('ntopik', $ntopik)
					->with('nama_topic', $daftar_nama_topic);
		}

		public function survey_masukan()
		{
			$data_nrp = Input::only(['nrp']);
			$nrp = $data_nrp['nrp'];

			$data_nama = Input::only(['nama']);
			$nama = $data_nama['nama'];

			$data_survey = Input::only(['survey']);
			$id_survey = $data_survey['survey'];

			$data_nilai = Input::only(['nilai']);
			$nilai = $data_nilai['nilai'];

			$data_dokumen = Input::only(['dokumen']);
			$dokumen = $data_dokumen['dokumen'];

			$simpan_nilai = new dbSurveyData;
			$simpan_nilai->id_survey = $id_survey;
			$simpan_nilai->nrp_penguji = $nrp;
			$simpan_nilai->dokumen = $dokumen;
			$simpan_nilai->nilai = $nilai;
			$simpan_nilai->save();

			return View::make('survey_masukan')
						->with('survey', $id_survey)
						->with('nrp', $nrp)
						->with('nama', $nama);
		}

		public function survey_selesai()
		{
			$data_nrp = Input::only(['nrp']);
			$nrp = $data_nrp['nrp'];

			$data_nama = Input::only(['nama']);
			$nama = $data_nama['nama'];

			$data_survey = Input::only(['survey']);
			$id_survey = $data_survey['survey'];

			$data_masukan = Input::only(['masukan']);
			$masukan = $data_masukan['masukan'];

			$simpan = new dbSurveyPenguji;
			$simpan->id_survey = $id_survey;
			$simpan->nrp = $nrp;
			$simpan->nama = $nama;
			$simpan->masukan = $masukan;
			$simpan->save();

			return View::make('survey_done');
		}

		public function survey_nilai()
		{
			$data_nrp = Input::only(['nrp']);
			$nrp = $data_nrp['nrp'];

			$data_nilai = Input::only(['nilai']);
			$nilai = $data_nilai['nilai'];

			return View::make('survey_read')
						->with('nilai', $nilai)
						->with('nrp', $nrp);
		}

		public function akurasi()
		{
			$dokumens = dbDokumen::where('training','=',false)->get();
			return View::make('akurasi')
					->with('data', $dokumens);
			
		}
	}
?>