<?php
	/**
	* 
	*/
	class dbSurveyData extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'survey_data';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>