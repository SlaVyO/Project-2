<?php
session_start();
require_once "config.php";
require_once "functions.php";
$dbcon = dbConnect($config);

$isActiv = isSessionActive($dbcon);

$titleHtml = 'Чтение Статеечек';

$errors = [];
$dbcon = dbConnect($config);

if (isset($_GET["id"]) && ($_GET['id'] != '')){
	$articleId = (int) trim($_GET["id"]);
	/*if ($result = getSelect(
				$dbcon, 
				["title","created_on"], 
				'sl_articles', 
				'id', 
				$articleId, 
				"LIMIT 1"))*/
	if ($result = getSelectAll($dbcon, 
							'sl_articles.user_id,
							 sl_articles.title,
							 sl_articles.content,
							 sl_articles.created_on,
							 sl_articles.updated_on,
							 sl_users.username', 
							'`sl_articles`', 
							'JOIN sl_users ON sl_users.id = sl_articles.user_id',
							["sl_articles.id" => $articleId])
							){
	foreach ($result[0] as $key => $value) {
		$articles[$key] = $value;
	}
		//print_r($result);	
	$outstr = "<span><strong> ".$articles['title']."</strong></span><br>";
	$outstr .= "<span class = \"smallgray \"> Автор: ".$articles['username']."</span><br>";
	$outstr .= "<span class = \"smallgray \"> Создана: ".$articles['created_on']." редактирована: ".$articles['updated_on']."</span><br>";
	$outstr .= "<span class = \"aticcontent \">".$articles['content']."</span><br><br>";
	}else{
		$outstr =  '<p style = "text-align: center;">
  		Что-то пошло не так ¯\_(ツ)_/¯ <br> Вероятно статья которую вы пытаетесь найти не существует<br><a href="./">Главная</a></p>';
	}

}else {
	$outstr =  '<p style = "text-align: center;">
  		Произошла ошибка и статья не была найденна ¯\_(ツ)_/¯ <br> <a href="./">Главная</a></p>';
	
}


require_once "head.php";
if ($isActiv){
	echo "<p style = \"text-align: right;\"><a href=\"./signout.php\">Выход</a></p><br><br>";
}
?>

<div class="showart">
<p style = "text-align: center;">
  		<?= $outstr ?>
</p>
</div>
<br><br><br>
<?php if ($isActiv){ require_once "link.html";}else {require_once "na_link.html";} ?>
</body>
</html>