<?php
session_start();
require_once "config.php";
require_once "functions.php";
$dbcon = dbConnect($config);

$isActiv = isSessionActive($dbcon);
$titleHtml = 'Все Статьи';

if (!$result = getSelectAll($dbcon, 
							'sl_articles.id,
							 sl_articles.user_id,
							 sl_articles.title,
							 LEFT(sl_articles.content , 100) AS content100 ,
							 sl_articles.created_on,
							 sl_articles.updated_on,
							 sl_users.username', 
							'`sl_articles`', 
							'JOIN sl_users ON sl_users.id = sl_articles.user_id')
							){
	strike();
}

$titleHtml = 'Все Статьи';
require_once "head.php";
if ($isActiv){
	echo "<p style = \"text-align: right;\"><a href=\"./signout.php\">Выход</a></p><br><br>";
}
?>

<table class="userbord">
	<tr>
		<?php if ($isActiv){ echo "<th>ID</th>";}	?>
		<th>Имя Автора</th> 
		<th>Заголовок</th>
		<th>Контент</th>
		<th>Подробнее</th>
		<th>Дата добавления</th>
		<th>Дата обновления</th>
		<?php if ($isActiv){ echo "<th></th><th></th>";	} ?>
		
	</tr>
	    <?php
	    foreach ($result as $value) {
	    echo "<tr>";
	    if ($isActiv){ echo"<td>".$value['id']."</td>";}
	    echo "<td>".$value['username']."</td>"
	    	."<td>".$value['title']."</td>"
	    	."<td>".$value['content100']."</td>"
	    	."<td><a href='./show_article.php?id=".$value['id']."'>...</a></td>"
	    	."<td>".$value['created_on']."</td>"
	    	."<td>".$value['updated_on']."</td>";
	    if ($isActiv){ echo"<td><a href='./change_article.php?id=".$value['id']."'>редактировать</a></td>"
	    	."<td><a href='./del_article.php?id=".$value['id']."'>удалить</a></td>";}
	    echo "</tr>";
	    }

	    ?>
</table>
<br><br><br>
<?php if ($isActiv){ require_once "link.html";}else {require_once "na_link.html";} ?>
 
</body>
</html>