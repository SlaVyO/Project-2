<?php
require_once "config.php";
require_once "functions.php";
$titleHtml = 'Регистрация';

$dbcon = dbConnect($config);
$errors = [];
$userInfo = array(
				'username' => '',
				'surname' => '',
				'email' => '',
				'password' => '',
				'created_on' => date('Y-m-d H:i:s'),//"'NOW()'",
				'updated_on' => date('Y-m-d H:i:s'),//"'NOW()'", 
			);

if ( isset( $_POST['do_post'] ) ){
	//добавить проверки на иньекции тримы и тд.
	if ($_POST['username'] !="" ){
		$userInfo['username'] = trim($_POST['username']);
	}
	else {
		$errors []= "Не указанно имя пользователя";
	}

	if ($_POST['surname'] !="" ){
		$userInfo['surname'] = trim($_POST['surname']); 
	}
	//else {$userInfo['surname' => '');}

	if ($_POST['email'] !="" ){
		$userInfo['email'] = trim($_POST['email']); 
	}
	else {
		$errors []= "Не указана почта пользователя";
	}

	if ($_POST['passwrd'] =="" ){
		$errors []= "Не указан пароль пользователя";
	}

	if ($_POST['passwrd_cnf'] =="" ){
		$errors []= "Не подтвержден пароль пользователя";
	}

	if ($_POST['passwrd'] != $_POST['passwrd_cnf'] ){
		$errors []= "Пароли не совпадают";		
	}
	else {

		//$template = "/^\S*(?=\S{5,})(?=\S*[a-zA-Z])(?=\S*[\W])(?=\S*[\d])\S*$/";
		$template = "/^\S*(?=\S{5,})(?=\S*[a-zA-Z])(?=\S*[\d])\S*$/";
		$result = preg_match($template, $_POST['passwrd'], $maches); 
		if (empty($maches)){
			$errors []= "Пароли не удовлетворяет условию";
		}else{
			$userInfo['password'] = password_hash($_POST['passwrd'], PASSWORD_DEFAULT);
		}
		
	}
}
else {$errors [] = ' ';}

if (empty($errors) && $dbcon){
	//username in db check
	$result = getSelect($dbcon, ['id'], 'sl_users', 'username', $userInfo['username'], "LIMIT 1");
	//var_dump($result);
	if ($result || empty ($result)) {
		if (count($result)!=0){
			$errors []= "Пользователь с таким именем уже существует";			
		}	
	} else {
			$errors []= "Ошибка добавления пользователя";
	}

	//email in db check
	$result = getSelect($dbcon, ['id'], 'sl_users', 'email', $userInfo['email'], "LIMIT 1");
	if ($result || empty ($result)) {
		if (count($result)!=0){
			$errors []= "Пользователь с такой почтой уже существует";			
		}	
	} else {
			$errors []= "Ошибка добавления пользователя";
	}
}


if (empty($errors)){

	//echo ("all valid");
	if (insertInto ($dbcon,  'sl_users', $userInfo, true)){
		$errors []= "Ваши данные были успешно внесены в в базы ЦРФ и Ми7";
		unset($userInfo);
		$userInfo = array(
				'username' => '',
				'surname' => '',
				'email' => '',
			);

	}
		
}

require_once "head.php";
?>

<br>
<div id="result_form"><?= $errors [0] ?></div> 


<form method="post" class='regform'>
  	
 <p>
 	<input placeholder='Input Username' type="text" name="username" class="username" value="<?= $userInfo['username']; ?>"/>
 </p>
  <p>
 	<input placeholder='Input Surname' type="text" name="surname" class="surname" value="<?= $userInfo['surname']; ?>"/>
 </p>
 <p>
 	<input placeholder='Input email address' type="email" name="email" class="email" value="<?= $userInfo['email']; ?>"/>
 </p>
 <p>
 	<input placeholder='Input Password' type="password" name="passwrd" class="passwrd" />
 </p>
 <p>
 	<input placeholder='Сonfirm Password' type="password" name="passwrd_cnf" class="passwrd_cnf" />
 </p>
 <p>
 	<input type="submit"  value="Регистрация" name="do_post" />
 </p>
  </form>
<p style = "text-align: center;">
  		Уже есть регистрация? <a href="./signin.php">Войти</a>
</p>
<p style = "text-align: center;">
  		Также имеется возможность посмотреть всех <a href="./all_user.php">Все давно здесь</a>
</p>
</body>
</html>
