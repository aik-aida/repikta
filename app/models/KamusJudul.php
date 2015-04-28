<?php
	/**
	* 
	*/
	class KamusJudul extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'kamus_judul';
		public $timestamps = false;
		protected $primaryKey = 'kata_dasar';
	}
?>