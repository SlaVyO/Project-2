<?php
session_start();
require_once "config.php";
require_once "functions.php";
$dbcon = dbConnect($config);
if (!isSessionActive($dbcon)){
	sessionExit();
	goBack();
}
$titleHtml = 'Удаление профиля';


$errors = [];
$flag = false;

if (isset($_GET["id"]) && ($_GET['id'] != '')){
	$userId = (int) trim($_GET["id"]);
	if ($result = getSelect(
				$dbcon, 
				["username"], 
				'sl_users', 
				'id', 
				$userId, 
				"LIMIT 1")){
		$userName = $result[0]['username'];
	/*foreach ($result[0] as $key => $value) {
		$userInfo[$key] = $value;
	}*/
		//print_r($result);	
	$outstr= "Вы действительно хотите грохнуть юзверя $userName? <a href='./del_user.php?id=$userId&del=ok'>Удалить</a>";
	$flag = true;
	}else{
		$outstr=  '<p style = "text-align: center;">
  		Что-то пошло не так ¯\_(ツ)_/¯ <br> Вероятно пользователя которого вы пытаетесь удалить больше не существует<br><a href="./">Главная</a>';
	}

}else {
	$outstr=  '<p style = "text-align: center;">
  		Произошла ошибка ¯\_(ツ)_/¯ <br> <a href="./">Главная</a>';
	
}


if (isset($_GET["del"]) && ($_GET['del'] == 'ok') && $flag){
	if (delArticleByUserId($dbcon, ['id'], 'sl_articles', 'user_id', $userId, "LIMIT 1" )){
		if (deliteNow($dbcon, 'sl_users', ['id' => $userId])){
		//delluser;
			$outstr= "Юзверь $userName успешно грохнулся! И забрал с собой все статеички :( если они у него были ;)";
		}
	}
}


require_once "head.php";

?>
<p style = "text-align: right;"><a href="./signout.php">Выход</a></p><br><br>
<p style = "text-align: center;">
  		<?= $outstr ?>
</p>
<p style = "text-align: center;">
  		Также имеется возможность посмотреть всех <a href="./all_user.php">Все давно здесь</a>
</p>
</body>
</html>