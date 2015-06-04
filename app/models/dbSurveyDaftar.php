<?php
	/**
	* 
	*/
	class dbSurveyDaftar extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'survey_daftar';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>