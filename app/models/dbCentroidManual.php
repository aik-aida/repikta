<?php
	/**
	* 
	*/
	class dbCentroidManual extends Eloquent 
	{
		protected $connection = 'repikta';
		protected $table = 'manual_centroid';
		public $timestamps = false;
		protected $primaryKey = 'id';
	}
?>