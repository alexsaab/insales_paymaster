<?php

require 'common.php';

if (isset($_GET['insales_id']))
{
	$insales_id = $_GET['insales_id'];
	if (!isset($data[$insales_id]))
	{
		die;
	}

	$_SESSION['insales_id'] = $insales_id;
	$_SESSION['token']      = md5(uniqid());

	header('Location: http://' . $data[$insales_id]['shop'] . '/admin/applications/' . $login . '/login?token=' . $_SESSION['token'] . '&login=http://' . $_SERVER['HTTP_HOST'] . '/login.php');
	die;
}
if (!isset($_SESSION['insales_id']) || !isset($data[$_SESSION['insales_id']]))
{
	die;
}

if (!isset($_SESSION['login']) || $_SESSION['login'] != 1)
{
	die;
}


$insales_id = $_SESSION['insales_id'];

if (isset($_POST['merchant_id']) && isset($_POST['secret_key']))
{
	$data[$insales_id]['merchant_id']      = $_POST['merchant_id'];
	$data[$insales_id]['secret_key']       = $_POST['secret_key'];
	$data[$insales_id]['payment_password'] = $_POST['payment_password'];
	$data[$insales_id]['hash_method'] = $_POST['hash_method'];
	$data[$insales_id]['vat_products'] = $_POST['vat_products'];
	$data[$insales_id]['vat_delivery'] = $_POST['vat_delivery'];

	file_put_contents('data', json_encode($data));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Прием платежей - Paymaster</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"
          integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ=="
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"
          integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-inverse navbar-static-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">Прием платежей - Paymaster</a>
        </div>

        <div class="navbar-header pull-right">
            <a class="navbar-brand"
               href="<?php echo 'https://' . $data[$_SESSION['insales_id']]['shop'] . '/admin2/dashboard'; ?>">Бэкофис
                Insales</a>
        </div>

        <div class="navbar-header pull-right">
            <a class="navbar-brand" href="https://paymaster.ru/Partners/authentication/login">Аторизация в Paymaster</a>
        </div>
        <div class="navbar-header pull-right">
            <a class="navbar-brand" href="https://info.paymaster.ru">Регистрация в Paymaster</a>
        </div>
    </div>
</nav>
<form method="POST" class="form-horizontal">
    <div class="form-group">
        <label class="col-sm-4 control-label">Идентификатор магазина в Insales</label>
        <div class="col-sm-4">
            <input type="text" readonly value="<?php echo $insales_id ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Пароль от внешнего способа оплаты в Insales</label>
        <div class="col-sm-4">
            <input type="text" name="payment_password"
                   value="<?php echo isset($data[$insales_id]['payment_password']) ? $data[$insales_id]['payment_password'] : '' ?>"
                   class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Идентификатор продавца в Paymaster</label>
        <div class="col-sm-4">
            <input type="text" name="merchant_id"
                   value="<?php echo isset($data[$insales_id]['merchant_id']) ? $data[$insales_id]['merchant_id'] : '' ?>"
                   class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Секретный ключ в Paymaster</label>
        <div class="col-sm-4">
            <input type="text" name="secret_key"
                   value="<?php echo isset($data[$insales_id]['secret_key']) ? $data[$insales_id]['secret_key'] : '' ?>"
                   class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Метод шифрования ключа</label>
        <div class="col-sm-4">
            <select class="form-control" id="hash_method"  name="hash_method">
                <option value="md5" <?php echo ($data[$insales_id]['hash_method']=='md5') ? 'selected' : ''; ?>>md5</option>
                <option value="sha1" <?php echo ($data[$insales_id]['hash_method']=='sha1') ? 'selected' : ''; ?>>sha1</option>
                <option value="sha256" <?php echo ($data[$insales_id]['hash_method']=='sha256') ? 'selected' : ''; ?>>sha256</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Ставка НДС для продуктов</label>
        <div class="col-sm-4">
            <select class="form-control" id="vat_products"  name="vat_products">
                <option value="vat18" <?php echo ($data[$insales_id]['vat_products']=='vat18') ? 'selected' : ''; ?>>НДС 18%</option>
                <option value="vat10" <?php echo ($data[$insales_id]['vat_products']=='vat10') ? 'selected' : ''; ?>>НДС 10%</option>
                <option value="vat118" <?php echo ($data[$insales_id]['vat_products']=='vat118') ? 'selected' : ''; ?>>НДС формула 18/118%</option>
                <option value="vat110" <?php echo ($data[$insales_id]['vat_products']=='vat110') ? 'selected' : ''; ?>>НДС формула 10/110%</option>
                <option value="vat0" <?php echo ($data[$insales_id]['vat_products']=='vat0') ? 'selected' : ''; ?>>НДС 0%</option>
                <option value="no_vat" <?php echo ($data[$insales_id]['vat_products']=='no_vat') ? 'selected' : ''; ?>>Без НДС</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Ставка НДС для доставки</label>
        <div class="col-sm-4">
            <select class="form-control" id="vat_delivery"  name="vat_delivery">
                <option value="vat18" <?php echo ($data[$insales_id]['vat_delivery']=='vat18') ? 'selected' : ''; ?>>НДС 18%</option>
                <option value="vat10" <?php echo ($data[$insales_id]['vat_delivery']=='vat10') ? 'selected' : ''; ?>>НДС 10%</option>
                <option value="vat118" <?php echo ($data[$insales_id]['vat_delivery']=='vat118') ? 'selected' : ''; ?>>НДС формула 18/118%</option>
                <option value="vat110" <?php echo ($data[$insales_id]['vat_delivery']=='vat110') ? 'selected' : ''; ?>>НДС формула 10/110%</option>
                <option value="vat0" <?php echo ($data[$insales_id]['vat_delivery']=='vat0') ? 'selected' : ''; ?>>НДС 0%</option>
                <option value="no_vat" <?php echo ($data[$insales_id]['vat_delivery']=='no_vat') ? 'selected' : ''; ?>>Без НДС</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-4 col-sm-4">
            <button type="submit" class="btn btn-default">Сохранить</button>
        </div>
    </div>
</form>
</body>
</html>