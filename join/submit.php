<?php


require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../dbconnect.php');


if(!empty($_POST)) {
  //エラーチェック

  if($_POST['name'] == '') {
    $err['name'] = 'blank';
  }
  if($_POST['email'] == '') {
    $err['email'] = 'blank';
  }
  if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
    $err['email'] = 'format';
  }
  if(strlen($_POST['password']) < 4) {
    $err['password'] = 'length';
  }
  if($_POST['password'] == '') {
    $err['password'] = 'blank';
  }

    $fileName = $_FILES['image']['name'];
     if (!empty($fileName)) {
     $ext = substr($fileName, -3);
     if($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
      $err['image'] = 'type';
     }
    }
  //重複アカウントのチェック
  if(empty($err)) {
    $member = $db->prepare('SELECT COUNT(*) AS cnt FROM users WHERE email=?');
    $member->execute(array($_POST['email']));
    $record = $member->fetch();
    if($record['cnt'] > 0) {
      $err['email'] = 'duplicate';
    }
  }

  if(empty($err)) {
    //画像をアップロード
    if($_FILES['image']['name'] != ''){

      $image = date('YmdHis') . $_FILES['image']['name'];
      move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../member_picture/' . $image);
      $_SESSION['join'] = $_POST;
      $_SESSION['join']['image'] = $image;
    }else {
      $_SESSION['join'] = $_POST;
    }
      header('Location: check.php');
    exit();
  }
}

//書き直し
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'rewrite') {
  $_POST = $_SESSION['join'];
  $err['rewrite'] = true;
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
        <h2>会員登録</h2>
        <p>次のフォームに必要事項を入力してください。</p>
        <dl>
          <dt>ニックネーム<span class="required">必須</span></dt>
          <dd><input type="text" name="name" size="35" maxlength="255" value="<?php if(isset($_POST['name'])) {echo h($_POST['name']);} ?>"></dd>
          <?php if (isset($err['name']) && $err['name'] == 'blank'): ?>
          <p class="error">ニックーネームを入力してください</p>
          <?php endif; ?>
          <dt>メールアドレス<span class="required">必須</span></dt>
          <dd><input type="text" name="email" size="35" maxlength="255" value="<?php if(isset($_POST['email'])) {echo h($_POST['email']);} ?>"></dd>
          <?php if (isset($err['email']) && $err['email'] == 'blank'): ?>
          <p class="error">メールアドレスを入力してください</p>
          <?php endif; ?>
          <?php if (isset($err['email']) && $err['email'] == 'format'): ?>
          <p class="error">E-mailの形式で入力してください</p>
          <?php endif; ?>
          <?php if (isset($err['email']) && $err['email'] == 'duplicate'): ?>
          <p class="error">指定されたメールアドレスはすでに登録されています</p>
          <?php endif; ?>
          <dt>パスワード<span class="required">必須</span></dt>
          <dd><input type="password" name="password" size="10" maxlength="20" value="<?php if(isset($_POST['password'])) {echo h($_POST['password']);} ?>"></dd>
          <?php if (isset($err['password']) && $err['password'] == 'blank'): ?>
          <p class="error">パスワードを入力してください</p>
          <?php endif; ?>
          <?php if (isset($err['password']) && $err['password'] == 'length'): ?>
          <p class="error">パスワードは4文字以上で入力してください</p>
          <?php endif; ?>
          <dt>プロフィール画像</dt>
          <dd>
            <input class="send-file" type="file" name="image" size="35">
            <?php if (isset($err['image']) && $err['image'] == 'type'): ?>
              <p class="error">写真などは「.gif」または「.jpg」の画像を指定してください</p>
            <?php endif; ?>
            <?php if (!empty($err)): ?>
              <p class="error">恐れ入りますが、画像を改めて指定してください</p>
            <?php endif; ?>
          </dd>
        </dl>
        <div><input type="submit" value="入力内容を確認する"></div>
      </form>
      <!-- <img src="photo_0.jpg" width="48" height="48"> -->
    </div>
  </body>
</html>
