<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ejemplos Webpay</title>
</head>
<body>
<?php
require_once '../vendor/autoload.php';

use Transbank\Webpay\Configuration;
use Transbank\Webpay\Webpay;
?>
<h1>Ejemplos Webpay - Transaccion Captura</h1>
<?php

/** Configuracion parametros de la clase Webpay */
$sample_baseurl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$configuration = Configuration::forTestingWebpayPlusCapture();

/** Creacion Objeto Webpay */
$webpay = new Webpay($configuration);

$action = isset($_GET["action"]) ? $_GET["action"] : 'init';

switch ($action) {

    default:

        $tx_step = "Init";
        $next_page = $sample_baseurl . "?action=capture";

        echo "<h2>Step: " . $tx_step . "</h2>";
        ?>

        <form id="formulario" action="<?php echo $next_page; ?>" method="post">
            <fieldset>
                <legend>Formulario de Captura</legend><br/><br/>
                <label>authorizationCode:</label>
                <input id="authorizationCode" name="authorizationCode" type="text" />&nbsp;&nbsp;&nbsp;
                <label>captureAmount:</label>
                <input id="captureAmount" name="captureAmount" type="text" />&nbsp;&nbsp;&nbsp;
                <label>buyOrder:</label>
                <input id="buyOrder" name="buyOrder" type="text" />&nbsp;&nbsp;&nbsp;<br/><br/><br/>
                <input name="enviar" type="submit" value="Enviar" />
            </fieldset>
        </form>

        <a href="../..">&laquo; volver a index</a>

        <?php

        die;

    case "capture":

        $tx_step = "Capture";

        /** Código de autorización de la transacción que se requiere capturar */
        $authorizationCode =  filter_input(INPUT_POST, 'authorizationCode');

        /** Monto que se desea capturar */
        $captureAmount = filter_input(INPUT_POST, 'captureAmount');

        /** Orden de compra de la transacción que se requiere capturar */
        $buyOrder = filter_input(INPUT_POST, 'buyOrder');

        $request = array(
            "authorizationCode" => $authorizationCode,
            "captureAmount" => $captureAmount,
            "buyOrder" => $buyOrder,
        );

        /** Iniciamos Transaccion */
        $result = $webpay->getCaptureTransaction()->capture($authorizationCode, $captureAmount, $buyOrder);

        if (isset($result->authorizationCode)) {
            $message = "Transacci&oacute;n capturada con exito en Webpay";
            $next_page = "";
        } else {
            $message = "webpay no disponible";
            $next_page = "";
        }

        break;
}

echo "<h2>Step: " . $tx_step . "</h2>";
?>

<div style="background-color:lightyellow;">
    <h3>request</h3>
<?php var_dump($request); ?>
</div>
<div style="background-color:lightgrey;">
    <h3>result</h3>
<?php var_dump($result); ?>
</div>
<p><samp><?php echo $message; ?></samp></p>
    <?php if (strlen($next_page)) { ?>
    <form action="<?php echo $next_page; ?>" method="post">
        <input type="hidden" name="token_ws" value="<?php echo $token; ?>">
        <input type="submit" value="Continuar &raquo;">
    </form>
<?php } ?>
<br>
<a href="../..">&laquo; volver a index</a>

</body>
</html>