<?php
	/**
	* 
	*/
	class dbTranskripDistanceAll extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'transkrip_distance_all';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>