<?php
	/**
	* 
	*/
	class KamusKata extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'kamus';
		public $timestamps = false;
		//protected $primaryKey = 'kata_dasar';
		protected $primaryKey = 'id';
	}
?>