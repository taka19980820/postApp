<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/dbconnect.php');

if (isset($_COOKIE['email']) && $_COOKIE['email'] != '') {
  $_POST['email'] = $_COOKIE['email'];
  $_POST['password'] = $_COOKIE['password'];
  $_POST['save'] = 'on';
}

if(!empty($_POST)) {
  //ログインの処理
  if($_POST['email'] != '' && $_POST['password'] != '') {
    $login = $db->prepare('SELECT * FROM users WHERE email=? AND password=?');
    $login->execute(array(
      $_POST['email'],
      sha1($_POST['password'])
    ));
    $member = $login->fetch();

    if($member){
      //ログイン成功
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();

      //ログイン情報を記録する
      if($_POST['save'] == 'on') {
        setcookie('email', $_POST['email'], time()+60*60*24*14);
        setcookie('password', $_POST['password'], time()+60*60*24*14);
      }

      header('Location: index.php'); exit();
    }else {
      $err['login'] = 'failed';
    }
  }else {
      $err['login'] = 'blank';
  }
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
        .checkbox {
          width: 10px;
        }
        .error {
          font-size: 12px!important;
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
      <h1>postApp</h1>
    </div>
    </header>
    <div id="container">
      <form class="submit" action="" method="post" enctype="multipart/form-data">
        <h2 class="lead-login">ログインする</h2>
        <p class="lead-login">次のフォームに必要事項を入力してください。</p>
        <p class="lead-login">入会手続きがまだの方はこちらからどうぞ</p>
        <p>&raquo;<a href="join/submit.php">入会手続きをする</a></p>

        <dl>
          <dt>メールアドレス<span class="required">必須</span></dt>
          <dd><input type="text" name="email" size="35" maxlength="255" value="<?php if(isset($_POST['email'])) {echo h($_POST['email']);} ?>"></dd>
          <?php if(isset($err['login']) && $err['login'] == 'blank'): ?>
            <p class="error">メールアドレスとパスワードをご記入してください</p>
          <?php endif; ?>
          <?php if(isset($err['login']) && $err['login'] == 'failed'): ?>
            <p class="error">ログインに失敗しました。正しくご記入ください。</p>
          <?php endif; ?>
          <dt>パスワード<span class="required">必須</span></dt>
          <dd><input type="password" name="password" size="10" maxlength="20" value="<?php if(isset($_POST['password'])) {echo h($_POST['password']);} ?>"></dd>
          <dt>ログイン情報の記録</dt>
          <dd><input class="checkbox" id="save" type="checkbox" name="save" value="on"><label for="save">次回から自動的にログインする</label></dd>
          </dl>
        <div class="submit-login">
          <input type="submit" value="ログインする">
        </div>
      </form>
      <!-- <img src="photo_0.jpg" width="48" height="48"> -->
    </div>
  </body>
</html>
