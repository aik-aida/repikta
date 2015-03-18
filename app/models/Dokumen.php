<?php
	/**
	* 
	*/
	class Dokumen extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'dokumen';
		public $timestamps = false ;
		protected $primaryKey = 'nrp';
	}
?>