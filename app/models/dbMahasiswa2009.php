<?php
	/**
	* 
	*/
	class dbMahasiswa2009 extends Eloquent
	{
		protected $connection = 'akademik';
		protected $table = 'mahasiswa2009';
		public $timestamps = false;
		protected $primaryKey = 'MA_nrp';
	}
?>