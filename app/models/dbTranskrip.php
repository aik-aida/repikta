<?php
	/**
	* 
	*/
	class dbTranskrip extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'transkrip';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>