<?php
	/**
	* 
	*/
	class dbAdministrator extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'administrator';
		public $timestamps = false;
		protected $primaryKey = 'id';
		//protected $primaryKey = 'id';
	}
?>