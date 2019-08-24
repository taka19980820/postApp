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
     <!-- <div id="fav" class="content"> -->
       <?php foreach ($favs as $fav): ?>
       <div class="msg" id="fav_<?php echo h($fav['id']); ?>" data-id="<?php echo h($fav['id']); ?>" data-name="<?php echo h($fav['name']); ?>" data-msg="<?php echo h($fav['message']); ?>">
         <img src="member_picture/<?php ($fav['picture'] != null) ? print h($fav['picture']) : print h('default_image.png') ?>" width="48" height="48">
         <p><?php echo h($fav['message']); ?><span class="name">(<?php echo h($fav['name']); ?>)</span></p>
           <p class="day"><?php echo h($fav['created']); ?></p><br>
           <a href="index.php?res=<?php echo h($fav['id']); ?>"><span class="res">返信</span></a>

           <?php
           $memberId = $member['id'];
           $postId = $fav['id'];
           $result = $postApp->getResult($postId);
            ?>

           <?php if($result['cnt'] == 1): ?>
           <span class="liked">お気に入り&nbsp;
             <span class="fav-count"><?php echo h($fav['likes']); ?></span>
           </span>
         <?php else: ?>
           <span class="favorite">お気に入り&nbsp;
             <span class="fav-count"><?php echo h($fav['likes']); ?></span>
           </span>
         <?php endif; ?>
           <?php if($_SESSION['id'] == $fav['user_id']): ?>
             <span class="delete delete_msg">削除</span>
           <?php endif; ?>
         </div>
       <?php endforeach; ?>
     <!-- </div> -->

       <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
       <script src="post.js"></script> -->
   </body>
 </html>
