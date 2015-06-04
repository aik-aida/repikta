<?php
	/**
	* 
	*/
	class dbSurveyPenguji extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'survey_penguji';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>