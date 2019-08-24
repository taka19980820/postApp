<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/Post.php');


$postApp = new Post();
$posts = $postApp->getAll();
$users = $postApp->getAllMembers();
$favs = $postApp->getFavMsg();
$member = $postApp->getMember();

if(!empty($_POST)) {
  $postApp->post();
  header('Location: index.php'); exit();
}
//返信
if(isset($_REQUEST['res'])) {
  $msg = $postApp->res();
}





 ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <p class="lead">つぶやく</p>
        <textarea id="new_msg" name="message" rows="5" cols="50"><?php if(isset($msg)) {echo h($msg);} ?></textarea>
        <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res']); ?>">
        <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
        <div class="bottun"><input type="submit" value="投稿する"></div>
      </form>
        <ul class="menu">
          <li><a href="#" class="active" data-id="msg">タイムライン</a></li>
          <li><a href="#" data-id="profile">プロフィール</a></li>
          <li><a href="#" data-id="fav">お気に入り</a></li>
          <li><a href="#" data-id="users">ユーザー</a></li>
          <li><a href="#" data-id="follows">フォロー</a></li>
        </ul>
     <!-- タイムライン -->
     <div id="msg" class="content active">
       <?php foreach ($posts as $post): ?>
         <div class="msg" id="msg_<?php echo h($post['id']); ?>" data-id="<?php echo h($post['id']); ?>" data-name="<?php echo h($post['name']); ?>" data-msg="<?php echo h($post['message']); ?>">
           <img src="member_picture/<?php ($post['picture'] != null) ? print h($post['picture']) : print h('default_image.png') ?>" width="48" height="48">
           <p><?php echo h($post['message']); ?><span class="name">(<?php echo h($post['name']); ?>)</span></p>
             <p class="day"><?php echo h($post['created']); ?></p><br>
             <a href="index.php?res=<?php echo h($post['id']); ?>"><span class="res">返信</span></a>

            <?php
            $memberId = $member['id'];
            $postId = $post['id'];
            $result = $postApp->getResult($postId);
             ?>
             <?php if($result['cnt'] == 1): ?>
             <span class="liked">お気に入り&nbsp;
               <span class="fav-count"><?php echo h($post['likes']); ?></span>
             </span>
           <?php else: ?>
             <span class="favorite">お気に入り&nbsp;
               <span class="fav-count"><?php echo h($post['likes']); ?></span>
             </span>
           <?php endif; ?>

             <?php if($_SESSION['id'] == $post['user_id']): ?>
               <span class="delete delete_msg">削除</span>
             <?php endif; ?>
           </div>
         <?php endforeach; ?>

     </div>

     <!-- プロフィール -->

          <div class="profile content" id="profile">
            <h1 class="user-name"><?php echo h($member['name']); ?></h1>
            <img src="member_picture/<?php ($member['picture'] != null) ? print h($member['picture']) : print h('default_image.png') ?>"  width="200" height="200">
            <p class="profile-text"><?php if(isset($member['profile'])) {echo h($member['profile']);} ?></p>
              <?php if(!isset($member['profile'])): ?>
              <div><a href="addProfile.php">プロフィールを追加する</a></div>
              <?php else: ?>
              <div><a href="editProfile.php">編集する</a></div>
              <?php endif; ?>
          </div>

          <!-- fav -->
          <div id="fav" class="content">

          </div>

          <!-- ユーザー -->
          <div id="users" class="content">

          </div>
          <!-- フォロー -->
          <div id="follows" class="content">

          </div>
    </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="post.js"></script>

  </body>
</html>
