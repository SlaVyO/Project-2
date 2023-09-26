<?php 
require_once "config.php";
require_once "functions.php";


$dbcon = dbconnect($config);

if (createtable ($dbcon, "sl_users", 
					[
					'id' => 'int(11) NOT NULL auto_increment', 
					'username' => 'varchar(250)  NOT NULL ',
					'surname' => 'varchar(250) ',
					'email' => 'varchar(250)  NOT NULL ',
					'password' => 'varchar(400)  NOT NULL ',
					'created_on' => 'datetime NOT NULL ',
					'updated_on' => 'datetime NOT NULL ',
					], 
					['id'],
					['username','email']
				)){
	echo "table sl_users is create";
}	

echo "<br/>";
if (createtable ($dbcon, "sl_articles", 
					[
					'id' => 'int(11) NOT NULL auto_increment', 
					'user_id' => 'int(11)  NOT NULL ',
					'title' => 'varchar(250) ',
					'content' => 'varchar(600)  NOT NULL ',
					'created_on' => 'datetime NOT NULL ',
					'updated_on' => 'datetime NOT NULL ',
					], 
					['id'],
					['title']
				)){
	echo "table sl_articles is create";
}	









?>