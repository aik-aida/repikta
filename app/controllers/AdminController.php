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
			return View::make('kmeans_main');
		}
	}
?>