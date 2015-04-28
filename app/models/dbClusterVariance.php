<?php
	/**
	* 
	*/
	class dbClusterVariance extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'cluster_variance';
		public $timestamps = false;
		protected $primaryKey = 'id';
	}
?>