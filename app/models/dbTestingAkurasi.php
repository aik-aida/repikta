<?php
	/**
	* 
	*/
	class dbTestingAkurasi extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'testing_akurasi';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>