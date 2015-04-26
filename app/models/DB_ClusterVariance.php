<?php
	/**
	* 
	*/
	class DB_ClusterVariance extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'cluster_variance';
		public $timestamps = false;
		protected $primaryKey = 'id';
	}
?>