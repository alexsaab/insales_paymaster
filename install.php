<?php
require 'common.php';

if (!isset($_GET['token']))
{
	die;
}

$token      = $_GET['token'];
$shop       = $_GET['shop'];
$insales_id = $_GET['insales_id'];

$data[$insales_id] = array(
	'shop'     => $shop,
	'password' => md5($token . $secret_key),
	'token' => $token,
);

file_put_contents('data', json_encode($data));