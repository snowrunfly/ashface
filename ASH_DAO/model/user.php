<?php

/*
	[UCenter] (C)2001-2099 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: user.php 1078 2011-03-30 02:00:29Z monkey $
*/

! defined ( 'IN_ASH' ) && exit ( 'Access Denied' );

class user {
	
	protected $user_id;
	protected $email;
	protected $name;
	protected $password; //here password should be explicit clear text
	protected $random_code;
	protected $activate_status;
	protected $expired_time;
	
	//TODO: This need to be implment 
	function __construct($user_id, $email, $name, $password, $random_code, $activate_status, $expired_time) {
		$this->user_id = $user_id;
		$this->email = $email;
		$this->name = $name;
		$this->password = $password;
		$this->random_code = $random_code;
		$this->activate_status = $activate_status;
		$this->expired_time = $expired_time;
	}
	
	//TODO: 
	public function __destruct() {
		echo 'Object was just destroyed <br>';
	}
	
	//TODO:
	public function __set($varname, $value) {
		$this->$varname = $value;
	}
	
	/**
	 * @return the $user_id
	 */
	public function getUser_id() {
		return $this->user_id;
	}
	
	/**
	 * @param field_type $user_id
	 */
	public function setUser_id($user_id) {
		$this->user_id = $user_id;
	}
	
	/**
	 * @return the $email
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * @param field_type $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}
	
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * @return the $password
	 */
	public function getPassword() {
		return $this->password;
	}
	
	/**
	 * @param field_type $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}
	
	/**
	 * @return the $random_code
	 */
	public function getRandom_code() {
		return $this->random_code;
	}
	
	/**
	 * @param field_type $random_code
	 */
	public function setRandom_code($random_code) {
		$this->random_code = $random_code;
	}
	
	/**
	 * @return the $activate_status
	 */
	public function getActivate_status() {
		return $this->activate_status;
	}
	
	/**
	 * @param field_type $activate_status
	 */
	public function setActivate_status($activate_status) {
		$this->activate_status = $activate_status;
	}
	
	/**
	 * @return the $expired_time
	 */
	public function getExpired_time() {
		return $this->expired_time;
	}
	
	/**
	 * @param field_type $expired_time
	 */
	public function setExpired_time($expired_time) {
		$this->expired_time = $expired_time;
	}
}

?>