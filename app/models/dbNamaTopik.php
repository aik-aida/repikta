<?php
	/**
	* 
	*/
	class dbNamaTopik extends Eloquent 
	{
		protected $connection = 'repikta';
		protected $table = 'nama_topik';
		public $timestamps = false;
		protected $primaryKey = 'id';
	}
?>