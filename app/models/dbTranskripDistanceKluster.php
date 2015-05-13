<?php
	/**
	* 
	*/
	class dbTranskripDistanceKluster extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'transkrip_distance_kluster';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>