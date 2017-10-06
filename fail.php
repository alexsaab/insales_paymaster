<?php
require 'common.php';
require 'lib.php';

$shop_id = $_POST['shop_id'];

if(!isset($data[$shop_id])) {
	die;
}

$shop = $data[$shop_id];

$insales_domain = $shop['shop'];
$api_key = $login;
$password = $shop['password'];

$insales_api = insales_api_client($insales_domain, $api_key, $password);
try {
	$order = $insales_api('GET', '/admin/orders/'.$_POST['LMI_PAYMENT_NO'].'.json');
	if(is_array($order)) {
		$paid = 0;
		$amount = $_POST['LMI_PAYMENT_AMOUNT'];
		$transaction_id = $_POST['transaction_id'];
		$key = $_POST['key'];

		$sign = paymasterGetSign($shop['merchant_id'],$order['id'],$amount,'RUB',$shop['secret_key'],$shop['hash_method']);

		if ($_POST["SIGN"] != $sign)
		{
			die;
		}

		echo '
    <form id="paymaster_form" action="http://'.$shop['shop'].'/payments/external/'.$order['payment_gateway_id'].'/fail" method="POST">
      <input type="hidden" name="paid" value="'.$paid.'">
      <input type="hidden" name="amount" value="'.$amount.'">
      <input type="hidden" name="key" value="'.$key.'">
      <input type="hidden" name="transaction_id" value="'.$transaction_id.'">
      <input type="hidden" name="signature" value="'.md5($shop_id.';'.$amount.';'.$transaction_id.';'.$key.';'.$paid.';'.$shop['payment_password']).'">
      <input type="hidden" name="shop_id" value="'.$shop_id.'">
      <script>document.getElementById("paymaster_form").submit();</script>
    </form>';
	}
}
catch (Exception $e) {
	echo $e->getMessage();
}

?>