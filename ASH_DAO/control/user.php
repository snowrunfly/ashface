<?php

/*
	[UCenter] (C)2001-2099 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: user.php 1059 2011-03-01 07:25:09Z monkey $
*/

! defined ( 'IN_ASH' ) && exit ( 'Access Denied' );

//todo
define ( 'ASH_USER_CHECK_USERNAME_FAILED', - 1 );
define ( 'ASH_USER_USERNAME_BADWORD', - 2 );
define ( 'ASH_USER_USERNAME_EXISTS', - 3 );
define ( 'ASH_USER_EMAIL_FORMAT_ILLEGAL', - 4 );
define ( 'ASH_USER_EMAIL_ACCESS_ILLEGAL', - 5 );
define ( 'ASH_USER_EMAIL_EXISTS', - 6 );

class usercontrol extends base {
	
	function __construct() {
		$this->usercontrol ();
	}
	
	function usercontrol() {
		parent::__construct ();
		$this->load ( 'user' );
		echo " usercontrol() invoked ";
	}
	
	//todo
	function onsynlogin() {
		$this->init_input ();
		$uid = $this->input ( 'uid' );
		if ($this->app ['synlogin']) {
			if ($this->user = $_ENV ['user']->get_user_by_uid ( $uid )) {
				$synstr = '';
				foreach ( $this->cache ['apps'] as $appid => $app ) {
					if ($app ['synlogin']) {
						$synstr .= '<script type="text/javascript" src="' . $app ['url'] . '/api/' . $app ['apifilename'] . '?time=' . $this->time . '&code=' . urlencode ( $this->authcode ( 'action=synlogin&username=' . $this->user ['username'] . '&uid=' . $this->user ['uid'] . '&password=' . $this->user ['password'] . "&time=" . $this->time, 'ENCODE', $app ['authkey'] ) ) . '" reload="1"></script>';
						if (is_array ( $app ['extra'] ['extraurl'] ))
							foreach ( $app ['extra'] ['extraurl'] as $extraurl ) {
								$synstr .= '<script type="text/javascript" src="' . $extraurl . '/api/' . $app ['apifilename'] . '?time=' . $this->time . '&code=' . urlencode ( $this->authcode ( 'action=synlogin&username=' . $this->user ['username'] . '&uid=' . $this->user ['uid'] . '&password=' . $this->user ['password'] . "&time=" . $this->time, 'ENCODE', $app ['authkey'] ) ) . '" reload="1"></script>';
							}
					}
				}
				return $synstr;
			}
		}
		return '';
	}
	
	//todo
	function onsynlogout() {
		$this->init_input ();
		if ($this->app ['synlogin']) {
			$synstr = '';
			foreach ( $this->cache ['apps'] as $appid => $app ) {
				if ($app ['synlogin']) {
					$synstr .= '<script type="text/javascript" src="' . $app ['url'] . '/api/' . $app ['apifilename'] . '?time=' . $this->time . '&code=' . urlencode ( $this->authcode ( 'action=synlogout&time=' . $this->time, 'ENCODE', $app ['authkey'] ) ) . '" reload="1"></script>';
					if (is_array ( $app ['extra'] ['extraurl'] ))
						foreach ( $app ['extra'] ['extraurl'] as $extraurl ) {
							$synstr .= '<script type="text/javascript" src="' . $extraurl . '/api/' . $app ['apifilename'] . '?time=' . $this->time . '&code=' . urlencode ( $this->authcode ( 'action=synlogout&time=' . $this->time, 'ENCODE', $app ['authkey'] ) ) . '" reload="1"></script>';
						}
				}
			}
			return $synstr;
		}
		return '';
	}
	
	//changed for dao test
	function onregister() {
		//$this->init_input ();
		$nickname = getgpc ( 'nickname' ); //$this->input ( 'nickname' );
		echo " nickname: " . $nickname;
		$password = getgpc ( 'password' ); // $this->input ( 'password' );
		$email = getgpc ( 'email' ); // $this->input ( 'email' );
		echo " email: " . $email;
		//		$questionid = $this->input('questionid');
		//		$answer = $this->input('answer');
		//		$regip = $this->input('regip');
		

//		if (($status = $this->_check_username ( $nickname )) < 0) {
//			return $status;
//		}
//		if (($status = $this->_check_email ( $email )) < 0) {
//			return $status;
//		}
		$uid = $_ENV ['user']->add_row ( $email, $nickname, $password );
		//$uid = $_ENV['user']->add_user($nickname, $password, $email, 0, $questionid, $answer, $regip);
		return $uid;
	}
	
	//todo
	function onedit() {
		$this->init_input ();
		$username = $this->input ( 'username' );
		$oldpw = $this->input ( 'oldpw' );
		$newpw = $this->input ( 'newpw' );
		$email = $this->input ( 'email' );
		$ignoreoldpw = $this->input ( 'ignoreoldpw' );
		$questionid = $this->input ( 'questionid' );
		$answer = $this->input ( 'answer' );
		
		if (! $ignoreoldpw && $email && ($status = $this->_check_email ( $email, $username )) < 0) {
			return $status;
		}
		$status = $_ENV ['user']->edit_user ( $username, $oldpw, $newpw, $email, $ignoreoldpw, $questionid, $answer );
		
		if ($newpw && $status > 0) {
			$this->load ( 'note' );
			$_ENV ['note']->add ( 'updatepw', 'username=' . urlencode ( $username ) . '&password=' );
			$_ENV ['note']->send ();
		}
		return $status;
	}
	
	//todo
	function onlogin() {
		$this->init_input ();
		$isuid = $this->input ( 'isuid' );
		$username = $this->input ( 'username' );
		$password = $this->input ( 'password' );
//		$checkques = $this->input ( 'checkques' );
//		$questionid = $this->input ( 'questionid' );
//		$answer = $this->input ( 'answer' );
		if ($isuid == 1) {
			$user = $_ENV ['user']->get_user_by_uid ( $username );
		} elseif ($isuid == 2) {
			$user = $_ENV ['user']->get_user_by_email ( $username );
		} else {
			$user = $_ENV ['user']->get_user_by_name ( $username );
		}
		
		$passwordmd5 = preg_match ( '/^\w{32}$/', $password ) ? $password : md5 ( $password );
		if (empty ( $user )) {
			$status = - 1;
		} elseif ($user ['password'] != md5 ( $passwordmd5 . $user ['salt'] )) {
			$status = - 2;
		} elseif ($checkques && $user ['secques'] != '' && $user ['secques'] != $_ENV ['user']->quescrypt ( $questionid, $answer )) {
			$status = - 3;
		} else {
			$status = $user ['uid'];
		}
		$merge = $status != - 1 && ! $isuid && $_ENV ['user']->check_mergeuser ( $username ) ? 1 : 0;
		return array ($status, $user ['username'], $password, $user ['email'], $merge );
	}
	
	//todo
	function oncheck_email() {
		$this->init_input ();
		$email = $this->input ( 'email' );
		return $this->_check_email ( $email );
	}
	
	//todo
	function oncheck_username() {
		$this->init_input ();
		$username = $this->input ( 'username' );
		if (($status = $this->_check_username ( $username )) < 0) {
			return $status;
		} else {
			return 1;
		}
	}
	
	//todo
	function onget_user() {
		$this->init_input ();
		$username = $this->input ( 'username' );
		if (! $this->input ( 'isuid' )) {
			$status = $_ENV ['user']->get_user_by_name ( $username );
		} else {
			$status = $_ENV ['user']->get_user_by_uid ( $username );
		}
		if ($status) {
			return array ($status ['uid'], $status ['username'], $status ['email'] );
		} else {
			return 0;
		}
	}
	
	function ongetprotected() {

	}
	
	//todo
	function ondelete() {
		$this->init_input ();
		$uid = $this->input ( 'uid' );
		return $_ENV ['user']->delete_user ( $uid );
	}
	
	function ondeleteavatar() {

	}
	
	function onaddprotected() {

	}
	
	function ondeleteprotected() {

	}
	
	function onmerge() {

	}
	
	function onmerge_remove() {

	}
	
	//todo
	function _check_username($username) {
		$username = addslashes ( trim ( stripslashes ( $username ) ) );
		if (! $_ENV ['user']->check_username ( $username )) {
			return ASH_USER_CHECK_USERNAME_FAILED;
		} elseif (! $_ENV ['user']->check_usernamecensor ( $username )) {
			return ASH_USER_USERNAME_BADWORD;
		} elseif ($_ENV ['user']->check_usernameexists ( $username )) {
			return ASH_USER_USERNAME_EXISTS;
		}
		return 1;
	}
	
	//todo
	function _check_email($email, $username = '') {
		if (! $_ENV ['user']->check_emailformat ( $email )) {
			return ASH_USER_EMAIL_FORMAT_ILLEGAL;
		} elseif (! $_ENV ['user']->check_emailaccess ( $email )) {
			return ASH_USER_EMAIL_ACCESS_ILLEGAL;
		} elseif (! $this->settings ['doublee'] && $_ENV ['user']->check_emailexists ( $email, $username )) {
			return ASH_USER_EMAIL_EXISTS;
		} else {
			return 1;
		}
	}
	
	function ongetcredit($arr) {

	}
	
	function onuploadavatar() {

	}
	
	function onrectavatar() {
		
	}
	
	function flashdata_decode($s) {
		$r = '';
		$l = strlen ( $s );
		for($i = 0; $i < $l; $i = $i + 2) {
			$k1 = ord ( $s [$i] ) - 48;
			$k1 -= $k1 > 9 ? 7 : 0;
			$k2 = ord ( $s [$i + 1] ) - 48;
			$k2 -= $k2 > 9 ? 7 : 0;
			$r .= chr ( $k1 << 4 | $k2 );
		}
		return $r;
	}

}

?>