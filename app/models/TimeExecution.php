<?php
	/**
	* 
	*/
	class TimeExecution
	{
		public function getTime(){
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
		}
	}
?>