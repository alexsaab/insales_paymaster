<?php

/**
 * Возвращаем HASH запроса
 * Вообще функция немного некрасиво написана так как в ней много переменных, но здесь пока только так
 *
 * @param        $merchant_id
 * @param        $order_id
 * @param        $amount
 * @param        $lmi_currency
 * @param        $secret_key
 * @param string $sign_method
 *
 * @return string
 */
function paymasterGetHash($LMI_MERCHANT_ID, $LMI_PAYMENT_NO, $LMI_SYS_PAYMENT_ID, $LMI_SYS_PAYMENT_DATE, $LMI_PAYMENT_AMOUNT, $LMI_CURRENCY, $LMI_PAID_AMOUNT, $LMI_PAID_CURRENCY, $LMI_PAYMENT_SYSTEM, $LMI_SIM_MODE, $SECRET, $hash_method = 'md5')
{
	$string = $LMI_MERCHANT_ID . ";" . $LMI_PAYMENT_NO . ";" . $LMI_SYS_PAYMENT_ID . ";" . $LMI_SYS_PAYMENT_DATE . ";" . $LMI_PAYMENT_AMOUNT . ";" . $LMI_CURRENCY . ";" . $LMI_PAID_AMOUNT . ";" . $LMI_PAID_CURRENCY . ";" . $LMI_PAYMENT_SYSTEM . ";" . $LMI_SIM_MODE . ";" . $SECRET;

	$hash = base64_encode(hash($hash_method, $string, true));

	return $hash;
}


/**
 * Возвращаем подпись
 *
 * @param        $merchant_id
 * @param        $order_id
 * @param        $amount
 * @param        $lmi_currency
 * @param        $secret_key
 * @param string $sign_method
 *
 * @return string
 */
function paymasterGetSign($merchant_id, $order_id, $amount, $lmi_currency, $secret_key, $sign_method = 'md5')
{

	$plain_sign = $merchant_id . $order_id . $amount . $lmi_currency . $secret_key;
	$sign       = base64_encode(hash($sign_method, $plain_sign, true));

	return $sign;
}


/**
 * Логирование в файл для отладки, на дурацкой настройке Joomla тут ничего не
 * работало
 *
 * @param $text
 *
 *
 * @since version
 */
function logF($text)
{
	$f = fopen(__DIR__ . "/payment.log", "a+");
	if (is_array($text))
	{
		foreach ($text as $key => $value)
		{
			fwrite($f, date('Y-m-d H:i:s') . " '{$key}' => '{$value}'\r\n");
		}
	}
	else
	{
		fwrite($f, date('Y-m-d H:i:s') . " " . $text . "\r\n");
	}
	fclose($f);
}