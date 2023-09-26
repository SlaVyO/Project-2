<?php
session_start();
require_once "config.php";
require_once "functions.php";
$dbcon = dbConnect($config);

if (!isSessionActive($dbcon)){
	sessionExit();
	goBack();
}

$titleHtml = 'Выход';

if (isset($_POST['do_post'])){
	sessionExit();
	goBack();
}

require_once "head.php";
?>

<form method="post" class='regform'>
<p style = "text-align: center;">
	Вы действительно хотите нас покинуть?
</p>
<p>
<input type="submit"  value="Нет, а шо делать?" name="do_post" />
</p>
</form>
<p style = "text-align: center;">
  		У вас есть еще и другие варианты! <a href="./">На главную</a>
</p>

</body>
</html>