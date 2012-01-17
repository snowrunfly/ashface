<?php

/*
========Get the user infomation=====
getUserByNickName($nickName); //nickName是字符串类型
getUserByEmail($email); /
getUserByEmailAndPasswd($email,$password);//password应当是明文。调用sha1转化为密文。

=======Create the User in DB=====
insertUser($User);   //There is no UserId, when insert the user to DB.

======Update the User=======
updateUserbyEmail($User);     //Use the email as the where condition  
updateUserbyNickname($User);  //Use the nickName as the where condition  
updateUserbyID($User);        //Use the userId as the where condition 
*/

 ! defined('IN_ASH') && exit('Access Denied');

require_once '../model/user.php';

class user_dao{
	
	var $db;
	var $dbconnection;
	var $user_tablename;
	
	function __construct(&$dbconnection){
		$this->user_dao($dbconnection);
	}
	
	function user_dao(&$dbconnection){
		$this->dbconnection = $dbconnection;
		$this->db = $dbconnection->db;
		$this->user_tablename = ASH_DBTABLEPRE . "user";
	}
	
	function get_user_by_id($userid){
		$arr = $this->db->fetch_first("SELECT * FROM " . $this->user_tablename . " WHERE user_id='$userid'");
		return $this->array_to_user($arr);
	}
	
	function get_user_by_name($username){
		$arr = $this->db->fetch_first("SELECT * FROM " . $this->user_tablename . " WHERE name='$username'");
		return $this->array_to_user($arr);
	}
	
	function get_user_by_email($email){
		$arr = $this->db->fetch_first("SELECT * FROM " . $this->user_tablename . " WHERE email='$email'");
		return $this->array_to_user($arr);
	}
	
	function get_user_by_email_psd($email, $password){
		//password应当是明文。调用sha1转化为密文。 
		$sha1psw = sha1($password);
		$arr = $this->db->fetch_first("SELECT * FROM " . $this->user_tablename . " WHERE email='$email' AND password='$sha1psw'");
		return $this->array_to_user($arr);
	}
	
	//There is no UserId, when insert the user to DB.
	function insert_user($user){
		//$user->password should be encrypted by sha1() when inserted to DB;
		$sha1psw = sha1($user->getPassword());
		$sql = "INSERT INTO " . $this->user_tablename 
		. " SET email='{$user->getEmail()}', name='{$user->getName()}', password='{$sha1psw}', " 
		. "random_code='{$user->getRandom_code()}', activate_status='{$user->getActivate_status()}', expired_time='{$user->getExpired_time()}'";
		$this->db->query($sql);
		$user->setUser_id($this->db->insert_id());
		return $user->getUser_id();
	}
	
	/*
	 * update should keep the unique keys as they were before
	 */
	//Use the email as the where condition
	function update_user_by_email($user){
		$sha1psw = sha1($user->getPassword());
		$sql = "UPDATE " . $this->user_tablename 
		. " SET name='{$user->getName()}', password='{$sha1psw}', random_code='{$user->getRandom_code()}', " 
		. "activate_status='{$user->getActivate_status()}', expired_time='{$user->getExpired_time()}' WHERE email='{$user->getEmail()}'";
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	//Use the name as the where condition
	function update_user_by_name($user){
		$sha1psw = sha1($user->getPassword());
		$sql = "UPDATE " . $this->user_tablename 
		. " SET password='{$sha1psw}', random_code='{$user->getRandom_code()}', " 
		. "activate_status='{$user->getActivate_status()}', expired_time='{$user->getExpired_time()}' WHERE name='{$user->getName()}'";
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	//Use the user_id as the where condition
	function update_user_by_id($user){
		$sha1psw = sha1($user->getPassword());
		$sql = "UPDATE " . $this->user_tablename 
		. " SET password='{$sha1psw}', random_code='{$user->getRandom_code()}', " 
		. "activate_status='{$user->getActivate_status()}', expired_time='{$user->getExpired_time()}' WHERE user_id={$user->getUser_id()}";
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	/*
	 * tool functions as below
	 */
	//encapsulation according to user's fields 
	function array_to_user($arr){
		$user = new user($arr ["user_id"], $arr ["email"], $arr ["name"], $arr ["password"], $arr ["random_code"], $arr ["activate_status"], $arr ["expired_time"]);
		return $user;
	}
	
	function check_username($username){
		$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
		$len = $this->dstrlen($username);
		if($len > 15 || $len < 3 || preg_match("/\s+|^c:\\con\\con|[%,\*\"\s\<\>\&]|$guestexp/is", $username)){
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	function dstrlen($str){
		if(strtolower(ASH_CHARSET) != 'utf-8'){
			return strlen($str);
		}
		$count = 0;
		for($i = 0; $i < strlen($str); $i ++ ){
			$value = ord($str [$i]);
			if($value > 127){
				$count ++ ;
				if($value >= 192 && $value <= 223)
					$i ++ ;
				elseif($value >= 224 && $value <= 239)
					$i = $i + 2;
				elseif($value >= 240 && $value <= 247)
					$i = $i + 3;
			}
			$count ++ ;
		}
		return $count;
	}
	
	function check_nameexists($username){
		$data = $this->db->result_first("SELECT username FROM " . $this->user_tablename . " WHERE name='$username'");
		return $data;
	}
	
	function check_emailformat($email){
		return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}
	
	function check_emailexists($email, $username = ''){
		$sqladd = $username !== '' ? "AND username<>'$username'": '';
		$email = $this->db->result_first("SELECT email FROM  " . $this->user_tablename . " WHERE email='$email' $sqladd");
		return $email;
	}
	
	function get_total_num($sqladd = ''){
		$data = $this->db->result_first("SELECT COUNT(*) FROM " . $this->user_tablename . " $sqladd");
		return $data;
	}
	
	function get_list($page, $ppp, $totalnum, $sqladd){
		$start = $this->dbconnection->page_get_start($page, $ppp, $totalnum);
		$data = $this->db->fetch_all("SELECT * FROM " . $this->user_tablename . " $sqladd LIMIT $start, $ppp");
		return $data;
	}
	
	function name2id($usernamesarr){
		$usernamesarr = daddslashes($usernamesarr, 1, TRUE);
		$usernames = $this->dbconnection->implode($usernamesarr);
		$query = $this->db->query("SELECT user_id FROM " . $this->user_tablename . " WHERE name IN($usernames)");
		$arr = array ();
		while($user = $this->db->fetch_array($query)){
			$arr [] = $user ['uid'];
		}
		return $arr;
	}
	
	function id2name($uidarr){
		$arr = array ();
		$query = $this->db->query("SELECT user_id, name FROM " . $this->user_tablename . " WHERE user_id IN (" . $this->dbconnection->implode($uidarr) . ")");
		while($user = $this->db->fetch_array($query)){
			$arr [$user ['uid']] = $user ['username'];
		}
		return $arr;
	}
}
?>