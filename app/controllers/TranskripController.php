<?php

	class TranskripController extends BaseController{
		

		public function read(){
			
			$filename = Input::file('file')->getClientOriginalName();
			$pathfile = './data/';
			Input::file('file')->move($pathfile, $filename);
			$file = $pathfile.$filename;

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
	
			return View::make('transkrip')
            	->with('data', $data_transkrip);
			// return View::make('showTranskrip')
			// 	->with('data', $data_transkrip);
		}
	}
?>