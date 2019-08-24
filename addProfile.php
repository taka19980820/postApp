<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/Post.php');

$postApp = new Post();
$member = $postApp->getMember();

if(!empty($_POST)){
  $postApp->addProfile();
  header('Location: index.php');
  exit();
}

 ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>postApp</title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <header>
      <div id="container">
      <h1>postApp</h1>
      <p class="logout"><a href="logout.php">ログアウトする</a></p>
       </div>
    </header>
    <div id="container">
      <form action="" method="post">
        <p class="lead">プロフィール文</p>
        <textarea name="profile" rows="5" cols="50"></textarea>
        <div class="bottun"><input type="submit" value="プロフィールを追加する"></div>
      </form>
    </body>
</html>
