<?php

define('DSN', 'mysql:dbhost=mysql8056.xserver.jp;dbname=takah1998_postappdb');
define('DB_USERNAME', 'takah1998_dbuser');
define('DB_PASSWORD', 'rhgu748g8');

try {
  $db = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
  echo $e->getMessage();
  exit();
}

 ?>
