<?php
	/**
	* 
	*/
	class CentroidGenerated extends Eloquent 
	{
		protected $connection = 'repikta';
		protected $table = 'generated_centroid';
		public $timestamps = false;
		protected $primaryKey = 'id';
	}
?>