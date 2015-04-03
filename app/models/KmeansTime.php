<?php
	/**
	* 
	*/
	class KmeansTime extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'kmeans_time';
		public $timestamps = false;
		protected $primaryKey = 'id';
	}
?>