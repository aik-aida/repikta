<?php
	/**
	* 
	*/
	class dbTestingRekomendasi extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'testing_rekomendasi';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>