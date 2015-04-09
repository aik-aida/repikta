<?php
	/**
	* 
	*/
	class KmeansResult extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'kmeans_result';
		public $timestamps = false;
		protected $primaryKey = 'id';
	}
?>