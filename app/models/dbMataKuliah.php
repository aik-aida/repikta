<?php
	/**
	* 
	*/
	class dbMataKuliah extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'matakuliah';
		public $timestamps = false ;
		protected $primaryKey = 'mk_kode';
	}
?>