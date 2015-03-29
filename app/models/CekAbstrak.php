<?php
	/**
	* 
	*/
	class CekAbstrak extends Eloquent
	{
		protected $connection = 'repikta';
		protected $table = 'cek_penulisan';
		public $timestamps = false;
		protected $primaryKey ='kata';
	}
?>