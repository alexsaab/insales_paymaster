<?php
require 'common.php';

$shop_id = 443196;

$shop = $data[$shop_id];

$insales_domain = $shop['shop'];
$api_key = $login;
$password = $shop['password'];

$insales_api = insales_api_client($insales_domain, $api_key, $password);


$deliveryVariants = $insales_api('GET', '/admin/delivery_variants.json');

$actualPayLink = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER[HTTP_HOST]}/pay.php";

$variantsArray = array();

foreach ($deliveryVariants as $deliveryVariant) {
	$variantsArray[] = array('delivery_variant_id' => $deliveryVariant['id']);
}

try {
	$admin  = $insales_api('POST', '/admin/payment_gateways.json', array(
		'payment_gateway' => array(
			'title' => 'PayMaster с онлайн кассой',
			'margin' => 0,
			'type' => 'PaymentGateway::External',
			'description' => 'PayMaster с онлайн кассой в соответствии с последними веяниями ФЗ-54. Пользуйтесь и соблюдайте закон!',
			'url' => $actualPayLink,
			'shop_id' => $shop_id,
			'payment_delivery_variants_attributes' => $variantsArray,
		)
	));
	echo "Метод оплаты успешно создан!";
} catch (Exception $e)
{
	echo $e->getMessage();
}

?>