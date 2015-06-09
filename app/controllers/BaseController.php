<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	protected function home()
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
			$kata_topik_bidang = array(); 

			for ($i=0; $i <$banyak_bidang ; $i++) { 
				array_push($nama_bidang, $main->GetKlusterName($i));
				$lda_result = dbLdaSave::where('percobaan_ke','=',$id_hasil_lda)
								->where('kluster_ke','=',$i)->get();
				

				$list_nrp = json_decode($lda_result[0]->daftar_dokumen);		//DAFTAR DOKUMEN PADA KLUSTER TERPILIH
				$theta_matrix = json_decode($lda_result[0]->matriks_theta);		//THETA LDA PADA KLUSTER TERPILIH
				$phi_matrix = json_decode($lda_result[0]->matriks_phi);			//PHI LDA PADA KLUSTER TERPILIH
				$k = $lda_result[0]->k_topik;									//JUMLAH TOPIK PADA KLUSTER TERPILIH
				$nterm = $lda_result[0]->n_term;								//BANYAK TERM LDA PADA KLUSTER TERPILIH
				$list_term = json_decode($lda_result[0]->matriks_term);			//DAFTAR TERM LDA PADA KLUSTER TERPILIH

				array_push($banyak_topik_bidang, $lda_result[0]->k_topik);
				$topic = $main->Get20TermTopic($k, $phi_matrix, $nterm, $list_term);
				array_push($kata_topik_bidang, $topic);
			}

			$nama_topik_bidang = array();
			for ($i=0; $i <$banyak_bidang ; $i++) { 
				$nama_topik = array();
				for ($j=0; $j < $banyak_topik_bidang[$i]; $j++) { 
					array_push($nama_topik, ('Topik '.($j+1)));
				}
				array_push($nama_topik_bidang, $nama_topik);
			}

		return View::make('home')
						->with('n_bidang', $banyak_bidang)
						->with('nama_bidang', $nama_bidang)
						->with('nama_topik_bidang', $nama_topik_bidang)
						->with('kata_topik_bidang', $kata_topik_bidang);
	}

	protected function login()
	{
		$data = Input::only(['username', 'password']);
		$uname = $data['username'];
		$pass = $data['password'];

		$data = dbAdministrator::all();
		$usr = $data[0]->username;
		$pwd = $data[0]->password;
		
		if($uname==$usr && $pass==$pwd){
			$request = Request::create('dashboard', 'GET', array());
			return Route::dispatch($request)->getContent();
			//return View::make('dashboard');
		} else {
			$request = Request::create('/', 'GET', array());
			return Route::dispatch($request)->getContent();
		}

	}

}
