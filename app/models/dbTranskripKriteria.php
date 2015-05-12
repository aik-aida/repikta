<?php
	/**
	* 
	*/
	class dbTranskripKriteria extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'transkrip_kluster';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>