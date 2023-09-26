<?php
session_start();
require_once "config.php";
require_once "functions.php";
$titleHtml = 'Вход';

$dbcon = dbConnect($config);
$errors = [];
$userInfo = array( 'email' => '',);

if ( isset( $_POST['do_post'] ) ){
	if ($_POST['email'] !="" ){
		$userInfo['email'] = trim($_POST['email']); 
	}
	else {
		$errors []= "Не указана почта пользователя";
	}
	
	if ($_POST['passwrd'] =="" ){
		$errors []= "Не указан пароль пользователя";
	}
}
else {$errors [] = ' ';}

if (empty($errors) && $dbcon){
	//username in db check
	$result = getSelect($dbcon, ['id','password','username'], 'sl_users', 'email', $userInfo['email'], "LIMIT 1");
	//var_dump($result);
	if ($result || empty ($result)) {
		if (count($result)!=0){
			foreach ($result[0] as $key => $value) {
				$userInfo[$key] = $value;
			}
		}else {
			$errors []= "Такого пользователя нет в системе";
		}	
	} else {
			$errors []= "Такого пользователя нет в системе";
	}
//print_r($userInfo);
	if (isset($userInfo['password']) && $userInfo['password'] != ""){
		if ( !password_verify( $_POST['passwrd'] , $userInfo['password'])){
			$errors []= "Похоже, что вы использовали неверный пароль. Обратитесь к администратору";
		}
	}else {
			$errors []= "Возникли проблемы с авторизацией";
	}

}



if (empty($errors)){

	//echo ("валидация успешна");
	$_SESSION['auth_user'] = $userInfo['id'];
	$_SESSION['username'] = $userInfo['username'];
	$errors [] = ' ';
	header("Location: ./");
		exit(); 
	//print_r($_SESSION);
}

require_once "head.php";
?>

<br>
<div id="result_form"><?= $errors [0] ?></div> 


<form method="post" class='signinform'>
  	
  <p>
 	<input placeholder='Input email address' type="email" name="email" class="email" value="<?= $userInfo['email']; ?>"/>
 </p>
 <p>
 	<input placeholder='Input Password' type="password" name="passwrd" class="passwrd" />
 </p>
 
 <p>
 	<input type="submit"  value="Войти" name="do_post" />
 </p>
  </form>
  <br>
  <br>
  <br>
  <br>
<p style = "text-align: center;">
  		Хотите зарегистрироваться? <a href="signup.php">Регистрация</a>
</p>
</body>
</html>
