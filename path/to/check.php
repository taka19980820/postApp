<?php

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../dbconnect.php');

if(!isset($_SESSION['join'])) {
  header('Location: index.php');
  exit();
}

if(!empty($_POST)){
  //登録処理
  $stmt = $db->prepare('INSERT INTO users SET name=?, email=?, password=?, picture=?, created=NOW()');
  $stmt->execute(array(
    $_SESSION['join']['name'],
    $_SESSION['join']['email'],
    sha1($_SESSION['join']['password']),
    $_SESSION['join']['image'],
  ));
  unset($_SESSION['join']);

  header('Location: thanks.php');
  exit();
}

 ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>postApp</title>
    <link rel="stylesheet" href="/../styles.css">
    <style>
      @media (max-width: 450px) {
        #container {
          width: 300px!important;
          margin: 0 auto!important;
        }
        form{
          width: 280px!important;
          margin: 15px auto!important;
        }
        h2 {
          font-size: 18px!important;
        }
        p {
          font-size: 14px!important;
        }
        input {
          width: 200px;
        }
        dt, dd {
          font-size: 14px!important;
        }
        .submit-btn {
          width: 70px;
        }
      }
      @media (max-width: 800px){
        #container{
          width: 600px;
          margin: 0 auto;
        }
      }
    </style>
  </head>
  <body>
    <header>
      <div id="container">
      <h1>postApp</h1>
    </div>
    </header>
    <div id="container">
      <p>記入した内容を確認して、「登録する」ボタンをクリックしてください。</p>
      <form class="submit" action="" method="post">
        <input type="hidden" name="action" value="submit">
        <dl>
          <dt>ニックネーム</dt>
          <dd><?php echo h($_SESSION['join']['name']); ?></dd>
          <dt>メールアドレス</dt>
          <dd><?php echo h($_SESSION['join']['email']); ?></dd>
          <dt>パスワード</dt>
          <dd>【表示されません】</dd>
          <dt>プロフィール画像</dt>
          <dd><img src="../member_picture/<?php echo h($_SESSION['join']['image']); ?>" width="100" height="100" alt=""></dd>
        </dl>
        <div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input class="submit-btn" type="submit" value="登録する"></div>
      </form>
      <!-- <img src="photo_0.jpg" width="48" height="48"> -->
    </div>
  </body>
</html>
