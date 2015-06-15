<?php
	/**
	* 
	*/
	class dbTranskripAhliDistanceKluster extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'transkripahli_distance_kluster';
		public $timestamps = false ;
		protected $primaryKey = 'id';
	}
?>