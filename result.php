<?php
require 'common.php';
require 'lib.php';

if (!isset($_POST["shop_id"]))
{
	die;
}
$shop_id = $_POST["shop_id"];
$shop    = $data[$shop_id];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if ($_POST["LMI_PREREQUEST"] == "1" || $_POST["LMI_PREREQUEST"] == "2")
	{
		echo "YES";
		die;
	}
	else
	{
		if (!isset($shop['hash_method']))
		{
			$shop['hash_method'] = 'md5';
		}

		$hash = paymasterGetHash($_POST["LMI_MERCHANT_ID"], $_POST["LMI_PAYMENT_NO"], $_POST["LMI_SYS_PAYMENT_ID"], $_POST["LMI_SYS_PAYMENT_DATE"], $_POST["LMI_PAYMENT_AMOUNT"], $_POST["LMI_CURRENCY"], $_POST["LMI_PAID_AMOUNT"], $_POST["LMI_PAID_CURRENCY"], $_POST["LMI_PAYMENT_SYSTEM"], $_POST["LMI_SIM_MODE"], $shop['secret_key'], $shop['hash_method']);

		$amount = sprintf("%.2f", $_POST["LMI_PAYMENT_AMOUNT"]);

		$sign = paymasterGetSign($_POST["LMI_MERCHANT_ID"], $_POST["LMI_PAYMENT_NO"], $amount, $_POST["LMI_CURRENCY"], $shop['secret_key'], $shop['hash_method']);



		if (($_POST["LMI_HASH"] == $hash) && ($_POST["SIGN"] == $sign))
		{
			$insales_domain = $shop['shop'];
			$api_key        = $login;
			$password       = $shop['password'];
			$insales_api    = insales_api_client($insales_domain, $api_key, $password);
			try
			{
				//Вот эта транзакция правильная 100% но почему то дело до нее не доходит, как будто это вообще не вызывается или что-то до него не доходит. Писал в PayMaster но никакого ответа пока не получил
				$order = $insales_api('PUT', '/admin/orders/' . $_POST['LMI_PAYMENT_NO'] . '.json', array(
					'order' => array(
						'financial_status' => 'paid'
					)));
				die;
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}
	}
}