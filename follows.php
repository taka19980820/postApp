<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/Post.php');


$postApp = new Post();
$posts = $postApp->getAll();
$users = $postApp->getAllMembers();
$favs = $postApp->getFavMsg();
$member = $postApp->getMember();
$followUsers = $postApp->getfollowUsers();

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
     <title>postApp</title>
     <link rel="stylesheet" href="styles.css">
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <?php foreach($followUsers as $followUser): ?>
       <div class="users" id="user_<?php echo h($followUser['id']); ?>" data-id="<?php echo h($followUser['id']); ?>">
           <img src="member_picture/<?php ($followUser['picture'] != null) ? print h($followUser['picture']) : print h('default_image.png') ?>" width="48" height="48">
           <p><?php echo h($followUser['name']); ?></p>

        <?php if($followUser['id'] != $member['id']): ?>
           <?php $stmt = $postApp->db->prepare('select count(*) as cnt from follows where user_id=? and followed_user_id=?');
           $stmt->execute(array($member['id'],$followUser['id']));
           $followerCnt = $stmt->fetch();
            $stmt = $postApp->db->prepare('select count(*) as cnt from followers where user_id=? and follower_id=?');
            $stmt->execute(array($member['id'],$followUser['id']));
            $mutualFollow = $stmt->fetch();
            ?>
           <?php if($followerCnt['cnt'] == 1): ?>
            <span class="followed">フォロー済み</span>
            <?php else: ?>
            <span class="follow">フォロー</span>
           <?php endif; ?>
           <?php if ($mutualFollow['cnt'] == 1): ?>
             <span class="mutualFollow">フォローされています</span>
           <?php endif; ?>
         <?php endif; ?>
        </div>
     <?php endforeach; ?>

       <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
       <script src="post.js"></script> -->
   </body>
 </html>
