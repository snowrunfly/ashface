<?php

/*
	[UCenter] (C)2001-2099 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: index.php 1059 2011-03-01 07:25:09Z monkey $
*/

define('IN_ASH', TRUE);
define('ASH_ROOT', dirname(__FILE__).'/');
echo 'ASH_ROOT:'.ASH_ROOT;


unset($GLOBALS, $_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);

$_GET		= daddslashes($_GET, 1, TRUE);
$_POST		= daddslashes($_POST, 1, TRUE);
$_COOKIE	= daddslashes($_COOKIE, 1, TRUE);
$_SERVER	= daddslashes($_SERVER);
$_FILES		= daddslashes($_FILES);
$_REQUEST	= daddslashes($_REQUEST, 1, TRUE);

if(!@include ASH_ROOT.'config.php') {
	exit('The file <b>config.php</b> does not exist, perhaps because of UCenter has not been installed, <a href="install/index.php"><b>Please click here to install it.</b></a>.');
}

$m = getgpc('m');
$a = getgpc('a');
//if(empty($m) && empty($a)) {
//	header('Location: admin.php');
//	exit;
//}

define('RELEASE_ROOT', '');

if(file_exists(ASH_ROOT.RELEASE_ROOT.'model/base.php')) {
	require ASH_ROOT.RELEASE_ROOT.'model/base.php';
} else {
	require ASH_ROOT.'model/base.php';
}

if(in_array($m, array('app', 'frame', 'user', 'pm', 'pm_client', 'tag', 'feed', 'friend', 'domain', 'credit', 'mail', 'version'))) {

	if(file_exists(ASH_ROOT.RELEASE_ROOT."control/$m.php")) {
		include ASH_ROOT.RELEASE_ROOT."control/$m.php";
	} else {
		include ASH_ROOT."control/$m.php";
	}
	
	$classname = $m.'control';
	echo $classname;
	$control = new $classname();
	$method = 'on'.$a;
	echo $method;
	if(method_exists($control, $method) && $a{0} != '_') {
		$data = $control->$method();
		echo is_array($data) ? $control->serialize($data, 1) : $data;
		exit;
	} elseif(method_exists($control, '_call')) {
		$data = $control->_call('on'.$a, '');
		echo is_array($data) ? $control->serialize($data, 1) : $data;
		exit;
	} else {
		exit('Action not found!');
	}

} else {

	exit('Module not found!');

}

$mtime = explode(' ', microtime());
$endtime = $mtime[1] + $mtime[0];

function daddslashes($string, $force = 0, $strip = FALSE) {
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force, $strip);
			}
		} else {
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}

function getgpc($k, $var='R') {
	switch($var) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		case 'R': $var = &$_REQUEST; break;
	}
	return isset($var[$k]) ? $var[$k] : NULL;
}

?>