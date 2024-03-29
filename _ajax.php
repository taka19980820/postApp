<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/Post.php');

$postApp = new Post();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  try{
    $res = $postApp->submit();
    header('Content-Type: application/json');
    echo json_encode($res);
    exit();
  }catch(Exception $e){
    header($_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500);
    echo $e->getMessage();
    exit();
  }
}

?>
