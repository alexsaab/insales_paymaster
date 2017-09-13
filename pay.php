<?php
require 'common.php';
require 'lib.php';

if (!isset($_POST['shop_id']))
{
	die;
}

$shop_id = $_POST['shop_id'];

if (!isset($data[$shop_id]))
{
	die;
}

$shop = $data[$shop_id];

$insales_domain = $shop['shop'];
$api_key        = $login;
$password       = $shop['password'];

$actualAppLink = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER[HTTP_HOST]}/";

$insales_api = insales_api_client($insales_domain, $api_key, $password);
try
{
	$order = $insales_api('GET', '/admin/orders/' . $_POST['order_id'] . '.json');
	if (is_array($order))
	{
		$amount = sprintf("%.2f", $_POST['amount']);
		$fields = array(
			'LMI_PAYMENT_AMOUNT' => $amount,
			'LMI_PAYMENT_DESC'   => "Оплата счета #" . $order['number'],
			'LMI_PAYMENT_NO'     => $order['id'],
			'LMI_MERCHANT_ID'    => $shop['merchant_id'],
			'LMI_CURRENCY'       => 'RUB',
			'shop_id'            => $shop_id,
			'key'                => $_POST['key'],
			'transaction_id'     => $_POST['transaction_id'],
			'SIGN'               => paymasterGetSign($shop['merchant_id'],$order['id'],$amount,'RUB',$shop['secret_key'],$shop['hash_method']),
			'LMI_PAYMENT_NOTIFICATION_URL' => $actualAppLink.'result.php',
			'LMI_SUCCESS_URL'    => $actualAppLink.'success.php',
			'LMI_FAIL_URL'       => $actualAppLink.'fail.php',
		);
		

		foreach ($order['order_lines'] as $key => $product)
		{
			$fields["LMI_SHOPPINGCART.ITEM[{$key}].NAME"]  = htmlspecialchars($product['title']);
			$fields["LMI_SHOPPINGCART.ITEM[{$key}].QTY"]   = $product['quantity'];
			$fields["LMI_SHOPPINGCART.ITEM[{$key}].PRICE"] = $product['full_sale_price'];
			$fields["LMI_SHOPPINGCART.ITEM[{$key}].TAX"]   = $shop['vat_products'];
		}
		
		

		// Теперь добавили доставку
		$key++;
		$fields["LMI_SHOPPINGCART.ITEM[{$key}].NAME"]  = htmlspecialchars($order['delivery_description']);
		$fields["LMI_SHOPPINGCART.ITEM[{$key}].QTY"]   = 1;
		$fields["LMI_SHOPPINGCART.ITEM[{$key}].PRICE"] = $order['full_delivery_price'];
		$fields["LMI_SHOPPINGCART.ITEM[{$key}].TAX"]   = $shop['vat_delivery'];

		$form = '<form id="paymaster_form" method="POST" action="https://paymaster.ru/Payment/Init">' . PHP_EOL;
		foreach ($fields as $key => $value)
		{
			$form .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' . PHP_EOL;
		}
		$form .= '<input type="submit" value="Оплатить" style="display: none">' . PHP_EOL . '</form>';
		$form .= '<script>document.getElementById("paymaster_form").submit();</script>';

		echo $form;
		die;
	}
}
catch (Exception $e)
{
	echo $e->getMessage();
}