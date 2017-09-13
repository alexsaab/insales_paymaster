<?php

header('Powered: test'); 
header('Content-Type: text/html; charset=utf-8');


require 'common.php';



if(!$_SESSION['insales_id'] || !$_GET['token']) {
  die;
}

$insales_id = $_SESSION['insales_id'];
if(!isset($data[$insales_id])) {
  die;
}


$token = $_GET['token'];
$user_email = $_GET['user_email'];
$user_name = $_GET['user_name'];
$user_id = $_GET['user_id'];
$token2 = $_GET['token2'];


if(md5($_SESSION['token'].$user_email.$user_name.$user_id.$data[$insales_id]['password']) != $token2) {
  die;
}

$_SESSION['login'] = 1;
header('Location: /index.php');

?>