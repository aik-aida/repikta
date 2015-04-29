<?php
	/**
	* 
	*/
	class CentroidManual extends Eloquent 
	{
		protected $connection = 'repikta';
		protected $table = 'manual_centroid';
		public $timestamps = false;
		protected $primaryKey = 'id';
	}
?>