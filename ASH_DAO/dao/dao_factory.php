<?php

/** 
 * @author Denny
 * 
 * 
 */
require_once 'dbconnection.php';
require_once 'user_dao.php';

class dao_factory{
	private static $instance;
	var $userdao;
	
	private function __construct(){
		$this->dao_factory();
	}
	
	private function dao_factory(){
		$dbconnection = new dbconnection();
		
		$this->userdao = new user_dao($dbconnection);
	
		//add other dao
	}
	
	public static function singleton(){
		if( ! isset(self::$instance)){
			echo 'Creating new instance.';
			//$className = __CLASS__;
			//self::$instance = new $className();
			self::$instance = new dao_factory();
		}
		return self::$instance;
	}
	
	//get objects' daos
	public function get_userdao(){
		return $this->userdao;
	}
	
	/**
	 * 
	 */
	function __destruct(){
		self::$instance = null;
	}

}

?>