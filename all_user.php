<?php
session_start();
require_once "config.php";
require_once "functions.php";
$dbcon = dbConnect($config);
if (!isSessionActive($dbcon)){
	sessionExit();
	goBack();
}
$titleHtml = 'Все пользователи';


if (!$result = getSelectAll($dbcon, '*', 'sl_users')){
	strike();
}
//print_r($result);

require_once "head.php";

?>
<p style = "text-align: right;"><a href="./signout.php">Выход</a></p><br><br>
<table class="userbord">
	<tr>
		<th>ID</th> 
		<th>Имя</th> 
		<th>Фамилия</th>
		<th>Почта</th>
		<th>Дата регистрации</th>
		<th>Дата обновления</th>
		<th></th> 
		<th></th> 
	</tr>
	    <?php
	    foreach ($result as $value) {
	    echo "<tr>".
	    "<td>".$value['id']."</td>"
	    	."<td>".$value['username']."</td>"
	    	."<td>".$value['surname']."</td>"
	    	."<td>".$value['email']."</td>"
	    	."<td>".$value['created_on']."</td>"
	    	."<td>".$value['updated_on']."</td>"
	    	."<td><a href='./change_user.php?id=".$value['id']."'>редактировать</a></td>"
	    	."<td><a href='./del_user.php?id=".$value['id']."'>удалить</a></td>"
	    	."</tr>";
	    }

	    ?>

</table>
<p style = "text-align: center;">
  		Срочно нужна новая регистрация? <br> <a href="./signup.php">Заходи (без смс)</a>
</p>
<p style = "text-align: center;">
  		Взгляните на наши прекласные статьи! <a href="./">Все статьи</a>
</p>	

</body>
</html>