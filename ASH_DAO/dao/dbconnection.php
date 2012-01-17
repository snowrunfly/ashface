<?php

/** 
 * @author Denny
 * 
 * 
 */
require_once '../lib/db.class.php';

class dbconnection {
	
	var $db;

	function __construct() {
		$this->dbconnection();
	}
	
	function dbconnection(){
		$this->init_db();
	}
	/**
	 * 
	 */
	function __destruct() {
		//TODO - Insert your code here
		if ($this->db != null) {
			$this->db->close();
		}
	}
	
	function init_db() {
		$this->db = new ashserver_db();
		$this->db->connect(ASH_DBHOST, ASH_DBUSER, ASH_DBPW, ASH_DBNAME, ASH_DBCHARSET, ASH_DBCONNECT, ASH_DBTABLEPRE);
	}
}

?>