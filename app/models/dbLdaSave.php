<?php
	/**
	* 
	*/
	class dbLdaSave extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'lda_saved';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>