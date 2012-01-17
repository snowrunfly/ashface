
<?php
define('IN_ASH', TRUE);
define('ASH_ROOT', dirname(__FILE__) . '/');
//echo 'ASH_ROOT:' . ASH_ROOT . '<br/>';


require_once '../config.php';
require_once '../dao/dao_factory.php';
require_once '../model/user.php';
require_once '../model/random_code.php';

$action = getgpc('a');

switch($action){
	case 'insert':
		insert_user();
		break;
	case 'query':
		query_user();
		break;
	case 'update':
		update_user();
		break;
	default:
		echo 'no action...please select action as below:<br><br><br><p>';
		break;
}

function generate_random_code(){
	return make_random_code();
}

function insert_user(){
	$user_id =  - 1;
	$email = getgpc('email');
	echo $email . '<br/>';
	$name = getgpc('name');
	echo $name . '<br/>';
	$password = getgpc('password');
	$random_code = getgpc('random_code');
	echo $random_code . '<br/>';
	$activate_status = 'true';
	$expired_time = '2012-01-20';
	
	$user = new user($user_id, $email, $name, $password, $random_code, $activate_status, $expired_time);
	echo dao_factory::singleton()->get_userdao()->insert_user($user);
}
function update_user(){
	$user_id = getgpc('user_id');
	$email = getgpc('email');
	$name = getgpc('name');
	$password = getgpc('password');
	$random_code = getgpc('random_code');
	$activate_status = getgpc('activate_status');
	$expired_time = getgpc('expired_time');
	
	$user = new user($user_id, $email, $name, $password, $random_code, $activate_status, $expired_time);
	$affected_rows=0;
	if(isset($user_id) && $user_id != ''){
		$affected_rows = dao_factory::singleton()->get_userdao()->update_user_by_id($user);
		echo 'update user by user_id <br>';
	}elseif(isset($name) && $name != ''){
		$affected_rows = dao_factory::singleton()->get_userdao()->update_user_by_name($user);
		echo 'update user by name <br>';
	}elseif(isset($email) && $email != ''){
		$affected_rows = dao_factory::singleton()->get_userdao()->update_user_by_email($user);
		echo 'update user by email <br>';
	}
	if($affected_rows!=0){
		echo 'updated user <br>';
		unset($user);
		unset($email);
		unset($name);
		unset($user_id);
	}else{
		echo "no updated user";
	}
}
function query_user(){
	$user_id = getgpc('user_id');
	$email = getgpc('email');
	$name = getgpc('name');
	$password = getgpc('password');
	
	$user;
	if(isset($user_id) && $user_id != ''){
		$user = dao_factory::singleton()->get_userdao()->get_user_by_id($user_id);
		echo 'get user by user_id <br>';
	}elseif(isset($name) && $name != ''){
		$user = dao_factory::singleton()->get_userdao()->get_user_by_name($name);
		echo 'get user by name <br>';
	}elseif(isset($email) && isset($password) && $email != '' && $password != ''){
		$sha1psw = sha1($password);
		$user = dao_factory::singleton()->get_userdao()->get_user_by_email_psd($email, $password);
		echo 'get user by email and password <br>';
	}elseif(isset($email) && $email != ''){
		$user = dao_factory::singleton()->get_userdao()->get_user_by_email($email);
		echo 'get user by email <br>';
	}
	if(isset($user)){
		echo $user->getUser_id() . ',' . $user->getName() . ',' . $user->getEmail() . ',' . $user->getPassword() . ',' . $user->getRandom_code() . ',' . $user->getActivate_status() . '<br>';
		unset($user);
		unset($email);
		unset($password);
		unset($name);
		unset($user_id);
	}else{
		echo "no existed user";
	}
}

function getgpc($k, $var = 'R'){
	switch($var){
		case 'G':
			$var = &$_GET;
			break;
		case 'P':
			$var = &$_POST;
			break;
		case 'C':
			$var = &$_COOKIE;
			break;
		case 'R':
			$var = &$_REQUEST;
			break;
	}
	return isset($var [$k]) ? $var [$k]: NULL;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>test database accessing</title>
</head>
<body>
注册信息：
<br />
<form name="form1" action="user_dao_test.php?a=insert" method="post">
邮箱：<input type="text" name="email" value="" /><br />
昵称：<input type="text" name="name" value="" /><br />
密码: <input type="password" name="password" value="" /> <br />
随机码:<input type="text" name="random_code"
	value="<?php
	echo generate_random_code();
	?>" readonly="readonly" /><br />
<input type="submit" name="submit" value="注册" /> <br />
</form>

<br>
查询信息：
<br>
<form name="form2" action="user_dao_test.php?a=query" method="post">ID:<input
	type="text" name="user_id" value="" /><br />
邮箱：<input type="text" name="email" value="" /><br />
昵称：<input type="text" name="name" value="" /><br />
密码: <input type="password" name="password" value="" /> <br />
<input type="submit" name="submit" value="查询" /> <br />
</form>

<br>
更新信息：
<br>
<form name="form3" action="user_dao_test.php?a=update" method="post">
ID:<input type="text" name="user_id" value="" /><br />
邮箱：<input type="text" name="email" value="" /><br />
昵称：<input type="text" name="name" value="" /><br />
随机码:<input type="text" name="random_code"
	value="<?php
	echo generate_random_code();
	?>" readonly="readonly" /><br />
有效期至：<input type="text" name="expired_time" value="" /><br />
激活状态：<input type="text" name="activate_status" value="" /><br />
<input type="submit" name="submit" value="更新" /> <br />
</form>
</body>
</html>
