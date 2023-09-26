<?php
session_start();
require_once "config.php";
require_once "functions.php";
$dbcon = dbConnect($config);
if (!isSessionActive($dbcon)){
	sessionExit();
	goBack();
}
$titleHtml = 'Редактирование профиля';

$errors = [];

$mailFlag = false;
$userNameFlag = false;

if (isset($_GET["id"]) && ($_GET['id']!='')){
	$userGetInfo['userid'] = (int) trim($_GET["id"]);
	if ($result = getSelect(
				$dbcon, 
				["username" , "surname", "email", "created_on", "updated_on"], 
				'sl_users', 
				'id', 
				$userGetInfo['userid'], 
				"LIMIT 1")){
		foreach ($result[0] as $key => $value) {
			$userGetInfo[$key] = $value;
		}
	}else{
		strike();
	}
}else{
		strike();
}

if ( isset( $_POST['do_post'] ) ){
	//добавить проверки на иньекции тримы и тд.
	
	$userPosInfo['userid'] = (int) trim($_POST["userid"]);

	if ($_POST['username'] !="" ){
		$userPosInfo['username'] = trim($_POST['username']);
	}else {
		$errors []= "Не указанно имя пользователя";
		$userPosInfo['username'] = $userGetInfo['username'];
	}

	if ($_POST['surname'] !="" ){
		$userPosInfo['surname'] = trim($_POST['surname']); 
	}else{
		$userPosInfo['surname'] = '';
	}
	
	if ($_POST['email'] !="" ){
		$userPosInfo['email'] = trim($_POST['email']); 
	}else {
		$errors []= "Не указана почта пользователя";
		$userPosInfo['email'] = $userGetInfo['email'];
	}

	if ($_POST['passwrd'] =="" && $_POST['passwrd_cnf'] !="" ){
		$errors []= "Не указан пароль пользователя";
	}

	if ($_POST['passwrd_cnf'] =="" && $_POST['passwrd'] !=""){
		$errors []= "Не подтвержден пароль пользователя";
	}

	if ($_POST['passwrd'] != $_POST['passwrd_cnf'] ){
		$errors []= "Пароли не совпадают";		
	}elseif($_POST['passwrd'] !='') {

	
		$template = "/^\S*(?=\S{5,})(?=\S*[a-zA-Z])(?=\S*[\d])\S*$/";
		$result = preg_match($template, $_POST['passwrd'], $maches); 
		if (empty($maches)){
			$errors []= "Пароли не удовлетворяет условию";
		}elseif($_POST['passwrd'] !='') {
			$userPosInfo['password'] = password_hash($_POST['passwrd'], PASSWORD_DEFAULT);
		}
	}
}else {
	foreach ($userGetInfo as $key => $value) {
		$userPosInfo[$key] = $value;
	}
	$errors [] = ' ';
}

if ($userPosInfo['email'] != $userGetInfo['email']){
	$mailFlag = true;
}
if ($userPosInfo['username'] != $userGetInfo['username']){
	$userNameFlag = true;
}



if (empty($errors) && $dbcon){
	//username in db check
	if ( $userNameFlag){
		$result = getSelect($dbcon, ['id'], 'sl_users', 'username', $userPosInfo['username'], "LIMIT 1");
		//var_dump($result);
		if ($result || empty ($result)) {
			if (count($result)!=0){
				$errors []= "Пользователь с таким именем уже существует";			
			}	
		} else {
				$errors []= "Ошибка добавления пользователя";
		}
	}
	//email in db check
	if ( $mailFlag){
		$result = getSelect($dbcon, ['id'], 'sl_users', 'email', $userPosInfo['email'], "LIMIT 1");
		if ($result || empty ($result)) {
			if (count($result)!=0){
				$errors []= "Пользователь с такой почтой уже существует";			
			}	
		} else {
				$errors []= "Ошибка добавления пользователя";
		}
	}
}



if (empty($errors)){
	//готовим апдейт массив
	if (isset($userPosInfo['password'])){
		$updArray['password'] = $userPosInfo['password'];
	}
	if ($userPosInfo['email'] != $userGetInfo['email']){
		$updArray['email'] = $userPosInfo['email'];
	}
	if ($userPosInfo['username'] != $userGetInfo['username']){
		$updArray['username'] = $userPosInfo['username'];
	}
	if ($userPosInfo['surname'] != $userGetInfo['surname']){
		$updArray['surname'] = $userPosInfo['surname'];
	}

	$updArray['updated_on'] = date('Y-m-d H:i:s');

	//print_r($updArray);

	if (letsUpdate($dbcon, 'sl_users', $updArray, ['id' => $userGetInfo['userid']])){
		$errors []="Данные были обновленны";
		if ($result = getSelect(
				$dbcon, 
				["username" , "surname", "email", "created_on", "updated_on"], 
				'sl_users', 
				'id', 
				$userGetInfo['userid'], 
				"LIMIT 1")){

			foreach ($result[0] as $key => $value) {
				$userPosInfo[$key] = $value;
				$userGetInfo[$key] = $value;
			}
		}
	}
}


require_once "head.php";

?>
<p style = "text-align: right;"><a href="./signout.php">Выход</a></p><br><br>
<br>
<div id="result_form"><?= $errors [0] ?></div> 

<p style = "text-align: center;">Пользователь <?= $userGetInfo['username']; ?> был создан <?= $userGetInfo['created_on']; ?><p>
	<p style = "text-align: center;">Дата последнего изменения <?= $userGetInfo['updated_on']; ?><p><br>
<form method="post" class='regform'>
  <p>
	<input type="hidden"  name="userid" value="<?= $userPosInfo['userid']; ?>">
</p>
 <p>
 	<input placeholder='Input Username' type="text" name="username" class="username" value="<?= $userPosInfo['username']; ?>"/>
 </p>
  <p>
 	<input placeholder='Input Surname' type="text" name="surname" class="surname" value="<?= $userPosInfo['surname']; ?>"/>
 </p>
 <p>
 	<input placeholder='Input email address' type="email" name="email" class="email" value="<?= $userPosInfo['email']; ?>"/>
 </p>
 <p>
 	<input placeholder='Input Password' type="password" name="passwrd" class="passwrd" />
 </p>
 <p>
 	<input placeholder='Сonfirm Password' type="password" name="passwrd_cnf" class="passwrd_cnf" />
 </p>
 <p>
 	<input type="submit"  value="Сохранить данные" name="do_post" />
 </p>
  </form>
<br>
<br>
<p style = "text-align: center;">
  		Также имеется возможность посмотреть всех <a href="./all_user.php">Все давно здесь</a>
 </p> 		
</body>
</html>