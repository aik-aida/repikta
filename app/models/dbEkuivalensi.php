<?php
	/**
	* 
	*/
	class dbEkuivalensi extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'ekuivalensi';
		public $timestamps = false ;
		protected $primaryKey = 'kode_mk_2014';
	}
?>