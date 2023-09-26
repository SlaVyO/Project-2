<?php
session_start();
require_once "config.php";
require_once "functions.php";
$dbcon = dbConnect($config);
if (!isSessionActive($dbcon)){
	sessionExit();
	goBack();
}
$titleHtml = 'Удаление Статеюшечек';

$errors = [];
$flag = false;

if (isset($_GET["id"]) && ($_GET['id'] != '')){
	$articleId = (int) trim($_GET["id"]);
	if ($result = getSelect(
				$dbcon, 
				["title"], 
				'sl_articles', 
				'id', 
				$articleId, 
				"LIMIT 1")){
		$articleTitle = $result[0]['title'];
	/*foreach ($result[0] as $key => $value) {
		$userInfo[$key] = $value;
	}*/
		//print_r($result);	
	$outstr= "Вы действительно хотите угробить труд удаляя статью \"$articleTitle\"? <a href='./del_article.php?id=$articleId&del=ok'>Удалить</a>";
	$flag = true;
	}else{
		$outstr=  '<p style = "text-align: center;">
  		Что-то пошло не так ¯\_(ツ)_/¯ <br> Вероятно статья которую вы пытаетесь удалить больше не существует<br><a href="./">Главная</a>';;
	}

}else {
	$outstr=  '<p style = "text-align: center;">
  		Произошла ошибка ¯\_(ツ)_/¯ <br> <a href="./">Главная</a>';
	
}

if (isset($_GET["del"]) && ($_GET['del'] == 'ok') && $flag){
	if (deliteNow($dbcon, 'sl_articles', ['id' => $articleId])){
	//delluser;
		$outstr= "Статья $articleTitle успешно удалена была, вероятно!";
	}
}

require_once "head.php";
?>
<p style = "text-align: right;"><a href="./signout.php">Выход</a></p><br><br>
<p style = "text-align: center;">
  		<?= $outstr ?>
</p>
<p style = "text-align: center;">
  		Также имеется возможность посмотреть всех пользователей <a href="./all_user.php">Все давно здесь</a>
</p>
<p style = "text-align: center;">
  		И посмотреть все статьи <a href="./">Все что набрано непосильным трудом</a>
</p>

</body>
</html>