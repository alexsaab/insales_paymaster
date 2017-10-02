<?php

require 'common.php';

$shop       = $_GET['shop'];
$token      = $_GET['token'];
$insales_id = $_GET['insales_id'];

$data = json_decode(file_get_contents('data'),true);


if (isset($data[$insales_id]) && ($insales_id))
{
	if (($data[$insales_id]['shop'] == $shop) && ($data[$insales_id]['password'] = $token))
	{
		unset($data[$insales_id]);
		file_put_contents('data', json_encode($data));
		echo 'OK';
	}
}
else
{
	echo 'FALSE';
}