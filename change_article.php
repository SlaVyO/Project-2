<?php
session_start();
require_once "config.php";
require_once "functions.php";
$dbcon = dbConnect($config);
if (!isSessionActive($dbcon)){
	sessionExit();
	goBack();
}
$titleHtml = 'Редактирование статьи';

$errors = [];
//$mailFlag = false;
//$userNameFlag = false;

if (!$result = getSelectAll($dbcon, '`id`,`username`', 'sl_users')){
	strike();
}
foreach ($result as $value) {
	$allUserArray[$value['id']] = $value['username'];
}


$dataArticle = array('content' => '',
			 'title' => '',
			 'user_id' => '',
			);

if (isset($_GET["id"]) && ($_GET['id']!='')){
	$articleGetId = (int) trim($_GET["id"]);
	if ($result = getSelect(
				$dbcon, 
				["title","user_id","content","created_on", "updated_on"], 
				'sl_articles', 
				'id', 
				$articleGetId, 
				"LIMIT 1"))	{
		
		foreach ($result[0] as $key => $value) {
			$dataGetArticle[$key] = $value;
		}
	}else{
		strike();
	}
}else{
		strike();
}

if ( isset( $_POST['do_post'] ) ){
	//добавить проверки на иньекции тримы и тд.
	
	if ($_POST['title'] !="" ){
		$dataPosArticle['title'] = trim($_POST['title']);
	}else {
		$errors []= "Не указан заголовок";
		$dataPosArticle['title'] = $dataGetArticle['title'];
	}

	if ($_POST['content'] !="" ){
		$dataPosArticle['content'] = trim($_POST['content']);
	}else {
		$errors []= "Не заполнен текст статьи";
		$dataPosArticle['content'] = $dataGetArticle['content'];
	}
	if (isset($_POST['user_id']) && $_POST['user_id'] !="" ){
		$dataPosArticle['user_id'] = trim($_POST['user_id']);
	}else {
		$errors []= "Не выбран автор";
		$dataPosArticle['user_id'] = $dataGetArticle['user_id'];
	}
}else {
	foreach ($dataGetArticle as $key => $value) {
		$dataPosArticle[$key] = $value;
	}
	$errors [] = ' ';
}



if (empty($errors)){
	//готовим апдейт массив
	
	if ($dataPosArticle['title'] != $dataGetArticle['title']){
		$updArray['title'] = $dataPosArticle['title'];
	}
	if ($dataPosArticle['user_id'] != $dataGetArticle['user_id']){
		$updArray['user_id'] = $dataPosArticle['user_id'];
	}
	if ($dataPosArticle['content'] != $dataGetArticle['content']){
		$updArray['content'] = $dataPosArticle['content'];
	}

	$updArray['updated_on'] = date('Y-m-d H:i:s');

	//print_r($updArray);

	if (letsUpdate($dbcon, 'sl_articles', $updArray, ['id' => $articleGetId])){
		$errors []="Данные были обновленны";
		if ($result = getSelect(
				$dbcon, 
				["title","user_id","content","created_on", "updated_on"], 
				'sl_articles', 
				'id', 
				$articleGetId, 
				"LIMIT 1")){

			foreach ($result[0] as $key => $value) {
				$dataPosArticle[$key] = $value;
				$dataGetArticle[$key] = $value;
			}
		}
	}
}


require_once "head.php";

?>
<p style = "text-align: right;"><a href="./signout.php">Выход</a></p><br><br>
<br>
<div id="result_form"><?= $errors [0] ?></div> 

<form method="post" class='regform'>
 
 <p>
 	<input placeholder='Input Article Title' type="text" name="title" value="<?= $dataPosArticle['title']; ?>"/>
 </p>
  <p>
  	<textarea rows="10" cols="45" name="content"><?= $dataPosArticle['content']; ?></textarea></p>
 <p>
 	<select name="user_id">
    
    <?php
    	if ($dataPosArticle['user_id'] != ''){
    		echo "<option disabled>Выберите Автора</option>";	
    	}else {
    	echo "<option selected disabled>Выберите Автора</option>";	
    	}
    	foreach ($allUserArray as $key => $value) {
    		if ($key!= $dataPosArticle['user_id']){
    			echo "<option value='".$key."'>".$value."</option>";
    		}else{
    			echo "<option selected value='".$key."'>".$value."</option>";
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


