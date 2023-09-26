<?php
session_start();
require_once "config.php";
require_once "functions.php";
$dbcon = dbConnect($config);
if (!isSessionActive($dbcon)){
	sessionExit();
	goBack();
}
$titleHtml = 'Создание статьи';

if (!$result = getSelectAll($dbcon, '`id`,`username`', 'sl_users')){
	strike();
}

$dataArticle = array('content' => '',
			 'title' => '',
			 'user_id' => '',
			);


if ( isset( $_POST['do_post'] ) ){
	//добавить проверки на иньекции тримы и тд.
	
	if (isset( $_POST['user_id'] ) && $_POST['user_id'] !="" ){
		$dataArticle['user_id'] = (int) trim($_POST['user_id']);
	}else {
		$errors []= "Не выбран автор";
	}

	if ($_POST['title'] !="" ){
		$dataArticle['title'] =  trim($_POST['title']);
	}else {
		$errors []= "Не указан заголовок";
	}
	if ($_POST['content'] !="" ){
		$dataArticle['content'] =  trim($_POST['content']);
	}else {
		$errors []= "Не контента статьи";
	}
}else {
	$errors [] = ' ';
}



//print_r($result);
if (empty($errors)){
	
	$dataArticle['created_on'] = date('Y-m-d H:i:s');
	$dataArticle['updated_on'] = date('Y-m-d H:i:s');
        
	
	if (insertInto ($dbcon, 'sl_articles', $dataArticle, true)){
		$errors []= "Ваши данные были успешно внесены в в базы ЦРФ и Ми7";
		unset($dataArticle);
		$dataArticle = array(
				'content' => '',
				'title' => '',
				'autor' => '',
			);
	}
}

require_once "head.php";
?>
<p style = "text-align: right;"><a href="./signout.php">Выход</a></p><br><br>
<br>
<div id="result_form"><?= $errors [0] ?></div> 

<form method="post" class='regform'>
 
 <p>
 	<input placeholder='Input Article Title' type="text" name="title" value="<?= $dataArticle['title']; ?>"/>
 </p>
  <p>
  	<textarea rows="10" cols="45" name="content"><?= $dataArticle['content']; ?></textarea></p>
 <p>
 	<select name="user_id">
    
    <?php
    	if ($dataArticle['user_id'] != ''){
    		echo "<option disabled>Выберите Автора</option>";	
    	}else {
    		echo "<option selected disabled>Выберите Автора</option>";	
    	}
    	
    	foreach ($result as $value) {
    		if ($value['id'] != $dataArticle['user_id']){
    			echo "<option value='".$value['id']."'>".$value['username']."</option>";
    		}else{
    			echo "<option selected value='".$value['id']."'>".$value['username']."</option>";
    		}
	   	}
    ?>
   </select>
 </p>
 <p>
 	<input type="submit"  value="Сохранить данные" name="do_post" />
 </p>
  </form>

  <br>
    <br>
      <br>
<p style = "text-align: center;">
  		Также имеется возможность посмотреть всех пользователей <a href="./all_user.php">Все давно здесь</a>
 </p>	
 <p style = "text-align: center;">
  		Взгляните на наши прекласные статьи! <a href="./">Все статьи</a>
</p>	
</body>
</html>


