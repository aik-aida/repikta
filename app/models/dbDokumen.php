<?php
	/**
	* 
	*/
	class dbDokumen extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'dokumen';
		public $timestamps = false ;
		protected $primaryKey = 'nrp';
	}
?>