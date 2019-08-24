<?php



class Post {
   private $_member;
   public $db;
   private $_likeCount = 0;

   public function __construct(){
     try {
       $this->_createToken();
       $this->db = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
       $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     }catch(PDOException $e){
       echo $e->getMessage();
       exit();
     }
     $this->_checkLogin();
   }

   private function _checkLogin() {
     if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
       //ログインしている
       $_SESSION['time'] = time();
       $this->_fetchMember();
     }else {
       //ログインしていない
       header('Location: login.php'); exit();
     }
   }
   private function _createToken() {
     if(!isset($_SESSION['token'])){
       $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
     }
   }
   private function _validateToken() {
     if(
       !isset($_SESSION['token']) ||
       !isset($_POST['token']) ||
       $_SESSION['token'] != $_POST['token']
     ){
       throw new Exception('invalid token!');
     }
   }



   private function _fetchMember() {
     $members = $this->db->prepare('SELECT * FROM users WHERE id=?');
     $members->execute(array($_SESSION['id']));
     $this->_member = $members->fetch();
   }

   public function getMember(){
     return $this->_member;
   }
   public function getAll(){
     $posts = $this->db->query("select u.name, u.picture, p.* from users u, posts p where u.id=p.user_id order by p.created desc");
     return $posts;
   }
   public function getFavMsg(){
     $memberId = $this->_member['id'];
     $favs = $this->db->query("select u.name, u.picture, p.* from users u, posts p inner join likes l on p.id=l.post_id where u.id=p.user_id and l.user_id=$memberId order by p.created asc");
     return $favs;
   }
   public function getResult($postId){
     $memberId = $this->_member['id'];
     $sql = sprintf("select count(*) as cnt from likes where user_id=$memberId and post_id=$postId");
     $stmt = $this->db->query($sql);
     $result = $stmt->fetch();
     return $result;
   }
   public function getfollowUsers(){
     $memberId = $this->_member['id'];
     $followUsers = $this->db->query("select * from users u inner join follows f on u.id=f.followed_user_id where f.user_id=$memberId");
     // $followUsers = $stmt->fetch();
     return $followUsers;
   }


   public function post() {
     $this->_validateToken();
     if($_POST['message'] != ''){
       $msg = $this->db->prepare('INSERT INTO posts SET user_id=?, message=?, reply_post_id=?, created=NOW()');
       $msg->execute(array(
         $this->_member['id'],
         $_POST['message'],
         $_POST['reply_post_id']
       ));
     }
   }
   public function submit() {
    if(!isset($_POST['mode'])){
      throw new Exception('mode not set!');
    }
    switch($_POST['mode']){
      case 'delete':
      return $this->_delete();
      case 'favorite':
      return $this->_favorite();
      case 'deleteFav':
      return $this->_deleteFav();
      case 'follow':
      return $this->_follow();
      case 'deleteFollow':
      return $this->_deleteFollow();
    }
   }

   private function _delete() {
     if(!isset($_POST['id'])){
       throw new Exception('[delete] id not set!');
     }
     $stmt = $this->db->prepare('DELETE FROM posts WHERE id=?');
     $stmt->execute(array($_POST['id']));

     return [];
   }
   private function _follow() {
     if(!isset($_POST['id'])){
       throw new Exception('[follow] id not set!');
     }
     // $myID = $this->_member['id'];
     // $followedUserId = $_POST['id'];
     $follow = $this->db->prepare('INSERT INTO follows set followed_user_id=?, user_id=?');
     $follow->execute(array(
       $_POST['id'],
       $this->_member['id']
     ));


     $follower = $this->db->prepare('INSERT INTO followers set user_id=?, follower_id=?');
     $follower->execute(array(
       $_POST['id'],
       $this->_member['id']
     ));
     return [];
   }
   private function _deleteFollow() {
     if(!isset($_POST['id'])){
       throw new Exception('[follow] id not set!');
     }
     // $myID = $this->_member['id'];
     // $followedUserId = $_POST['id'];
     $deleteFollow = $this->db->prepare('DELETE FROM follows where followed_user_id=?');
     $deleteFollow->execute(array($_POST['id']));


     $deleteFollower = $this->db->prepare('DELETE FROM followers WHERE follower_id=?');
     $deleteFollower->execute(array($this->_member['id']));
     return [];
   }

   public function res(){
     $res = $this->db->prepare("select u.name, u.picture, p.* from users u, posts p where u.id=p.user_id AND p.id=? order by p.created desc");
     $res->execute(array($_REQUEST['res']));

     $table = $res->fetch();
     $msg = ' ' . '≫' . '@' . $table['name'] . ' ' . $table['message'];
     return $msg;
   }
   public function addProfile(){
     $stmt = $this->db->prepare("UPDATE users SET profile=? WHERE id=?");
     $stmt->execute(array(
        $_POST['profile'],
        $this->_member['id']
     ));
   }
   public function editProfile(){
     $stmt = $this->db->prepare("UPDATE users SET profile=? WHERE id=?");
     $stmt->execute(array(
        $_POST['profile'],
        $this->_member['id']
     ));
   }

   public function getAllMembers(){
     $users = $this->db->query("SELECT * FROM users ORDER BY created DESC");
     return $users;
   }

   private function _favorite(){
     if(!isset($_POST['id'])){
       throw new Exception('[favorite] id not set!');
     }
     $postId = $_POST['id'];
     $userId = $this->_member['id'];
     $stmt = $this->db->prepare('select * from posts where id=?');
     $stmt->execute(array($postId));
     $row = $stmt->fetch();
     $n = $row['likes'];

     $this->db->query("update posts set likes=$n+1 where id=$postId");
     $sql = sprintf("select likes from posts where id=$postId");
     $stmt = $this->db->query($sql);
     $this->_likeCount = $stmt->fetchColumn();
     $this->db->query("insert into likes(user_id, post_id, created) values ($userId, $postId, now())");

     return [
       'likes' => $this->_likeCount
     ];
   }
   private function _deleteFav(){
     if(!isset($_POST['id'])){
       throw new Exception('[deletefav] id not set!');
     }
     $postId = $_POST['id'];
     $userId = $this->_member['id'];
     $stmt = $this->db->prepare('select * from posts where id=?');
     $stmt->execute(array($postId));
     $row = $stmt->fetch();
     $n = $row['likes'];

     $this->db->query("delete from likes where post_id=$postId and user_id=$userId");
     $this->db->query("update posts set likes=$n-1 where id=$postId");
     $sql = sprintf("select likes from posts where id=$postId");
     $stmt = $this->db->query($sql);
     $this->_likeCount = $stmt->fetchColumn();


     return [
       'likes' => $this->_likeCount
     ];
   }




}

 ?>
