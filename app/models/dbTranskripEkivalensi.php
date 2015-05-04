<?php
	/**
	* 
	*/
	class dbTranskripEkivalensi extends Eloquent
	{
		protected $connection = 'akademik';
		protected $table = 'transkripj_ekuivalensi';
		public $timestamps = false;
		protected $primaryKey = 'id';
	}
?>