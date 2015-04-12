<?php
	/**
	* 
	*/
	class AdminController extends BaseController
	{
		public function kamus_list(){
			$kamus = KamusKata::get();
			return View::make('kamus_main')
					->with('kamus', $kamus);
		}

		public function dokumen_list(){
			$corpus = Dokumen::get();
			return View::make('dokumen_main')
					->with('corpus', $corpus);
		}

		public function dokumen_detail(){
			$data = Input::only(['iddoc']);
			$id = $data['iddoc'];

			$dokumen_detail = Dokumen::find($id);

			return View::make('dokumen_detail')
					->with('dokumen', $dokumen_detail);
		}

		public function dokumen_tf(){
			$data = Input::only(['iddoc']);
			$id = $data['iddoc'];

			$kamus = KamusKata::get();
			$dokumen_detail = Dokumen::find($id);
			$vectortf = json_decode($dokumen_detail->nilai_tf);

			return View::make('dokumen_tf')
					->with('id', $id)
					->with('kamus', $kamus)
					->with('vectortf', $vectortf);
		}

		public function dokumen_tfidf(){
			$data = Input::only(['iddoc']);
			$id = $data['iddoc'];

			$kamus = KamusKata::get();
			$dokumen_detail = Dokumen::find($id);
			$vectortfidf = json_decode($dokumen_detail->nilai_tfidf);

			return View::make('dokumen_tfidf')
					->with('id', $id)
					->with('kamus', $kamus)
					->with('vectortfidf', $vectortfidf);	
		}

		public function centroid_list(){
			$gencen = CentroidGenerated::get();
			return View::make('centroid_main')
					->with('centroids', $gencen);
		}

		public function centroid_detail(){
			$data = Input::only(['idcentroid']);
			$id = $data['idcentroid'];
			//echo $id;
			$kamus = KamusKata::get();
			$centroid_obj = CentroidGenerated::find($id);
			$centroid = json_decode($centroid_obj->centroid);
			$corpus = json_decode($centroid_obj->dokumen);
			// echo count($centroid_obj)."<br />";
			// var_dump($centroid);

			return View::make('centroid_detail')
					->with('id', $id)
					->with('data', $centroid)
					->with('corpus', $corpus)
					->with('kamus', $kamus);
		}

		public function kluster_list(){
			
			$jumlah_iterasi = array();
			$total_waktu = array();
			$kluster = array();
			$id_first = array();


			$data = KmeansResult::get();

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
					$dokumen = DB::table('dokumen')->select('nrp', 'nama', 'judul_ta')->where('nrp', '=' , $iddoc)->get();
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
	}
?>