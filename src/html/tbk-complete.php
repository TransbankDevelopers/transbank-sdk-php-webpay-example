<?php
require_once '../vendor/autoload.php';

use Transbank\Webpay\Configuration;
use Transbank\Webpay\Webpay;
?>
<h1>Ejemplos Webpay - Transaccion Complete</h1>
<?php

session_start();

/* Configuracion parametros de la clase Webpay */
$sample_baseurl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$configuration = Configuration::forTestingWebpayPlusMall();

/* Creacion Objeto Webpay */
$webpay = new Webpay($configuration);

$action = isset($_GET["action"]) ? $_GET["action"] : 'init';

switch ($action) {

    default:

        $tx_step = "Init";

        /** Monto de la transacción */
        $amount = 9990;

        /** Orden de compra del comercio */
        $buyOrder = rand();

        /** (Opcional) Identificador de sesión */
        $sessionId = uniqid();

        /** Fecha de expiración de tarjeta, formato YY/MM */
        $cardExpirationDate = "18/04";

        /** Código de verificación de la tarjeta */
        $cvv = "123";

        /** Número de la tarjeta */
        $cardNumber = "4051885600446623";

        $request = array(
            "amount" => $amount,
            "buyOrder" => $buyOrder,
            "sessionId" => $sessionId,
            "cardExpirationDate" => $cardExpirationDate,
            "cvv" => $cvv,
            "cardNumber" => $cardNumber,
        );

        /** Iniciamos Transaccion */
        $result = $webpay->getCompleteTransaction()->initCompleteTransaction($amount, $buyOrder, $sessionId, $cardExpirationDate, $cvv, $cardNumber);

        /** Verificamos respuesta de inicio en webpay */
        if (!empty($result->token) && strlen($result->token)) {
            $message = "Sesion iniciada con exito en Webpay";
            $next_page = $sample_baseurl . "?action=queryshare";
            $token = $result->token;

        } else {
            $message = "webpay no disponible";
        }

        $button_name = "Continuar &raquo;";
        break;

    case "queryshare" :

        $tx_step = "QueryShare";

        if (!isset($_POST["token_ws"]))
            break;

        /** Token de la transacción */
        $token = filter_input(INPUT_POST, 'token_ws');

        /** Orden de compra de la transacción */
        $buyOrder = filter_input(INPUT_POST, 'buyOrder');

        /** Número de cuotas */
        $shareNumber = 2;

        $request = array(
            "token" => $token,
            "buyOrder" => $buyOrder,
            "shareNumber" => $shareNumber,
        );

        $result = $webpay->getCompleteTransaction()->queryShare($token, $buyOrder, $shareNumber);

        /** Verificamos respuesta de inicio en webpay */
        if (!empty($result->token) && strlen($result->token)) {

            $message = "Transacci&oacute;n realizada con exito en Webpay";
            $next_page = $sample_baseurl . "?action=authorize";
            $token = $result->token;

            $idQueryShare = $result->queryId;

        } else {
            $message = "webpay no disponible";
        }

        $button_name = "Continuar &raquo;";
        break;

    case "authorize" :

        $tx_step = "Authorize";

        if (!isset($_POST["token_ws"]))
            break;

        /** Token de la transacción */
        $token = filter_input(INPUT_POST, 'token_ws');

        /** Orden de compra de la transacción */
        $buyOrder =  filter_input(INPUT_POST, 'buyOrder');

        /** (Opcional) Flag que indica si aplica o no periodo de gracia */
        $gracePeriod = false;

        /** Identificador de la consulta de cuota */
        $idQueryShare = filter_input(INPUT_POST, 'queryId');

        /** (Opcional) Lista de contiene los meses en los cuales se puede diferir el pago, y el monto asociado a cada periodo */
        $deferredPeriodIndex = 0;

        $request = array(
            "token" => $token,
            "buyOrder" => $buyOrder,
            "gracePeriod" => $gracePeriod,
            "idQueryShare" => $idQueryShare,
            "deferredPeriodIndex" => $deferredPeriodIndex
        );

        $result = $webpay->getCompleteTransaction()->authorize($token, $buyOrder, $gracePeriod, $idQueryShare, $deferredPeriodIndex);

        /** Verificamos respuesta de inicio en webpay */
        if ($result->detailsOutput->responseCode === 0) {

            $authorizationCode = $result->detailsOutput->authorizationCode;

            $message = "Transacci&oacute;n realizada con exito en Webpay";
            $next_page = $sample_baseurl . "?action=end";

        } else {

            $message = "webpay no disponible";
        }

        $button_name = "Continuar &raquo;";
        break;

    case "end" :

        $tx_step = "End";

        $request = '';
        $result = $_POST;
        $message = "Transacion Finalizada";
        $next_page = $sample_baseurl."?action=nullify";
        $button_name = "Anular Transacci&oacute;n &raquo;";

        /** Token de la transacción */
        $token = filter_input(INPUT_POST, 'token_ws');

        /** Orden de compra de la transacción */
        $buyOrder = filter_input(INPUT_POST, 'buyOrder');

        /** Identificador de la consulta de cuota */
        $idQueryShare = filter_input(INPUT_POST, 'queryId');

        /** Codigo de Autorización */
        $authorizationCode = filter_input(INPUT_POST, 'authorizationCode');

        break;

    case "nullify":

        $tx_step = "nullify";

        /** Codigo de Comercio */
        $commercecode = null;

        /** Código de autorización de la transacción que se requiere anular */
        $authorizationCode = filter_input(INPUT_POST, 'authorizationCode');

        /** Monto autorizado de la transacción que se requiere anular */
        $authorizedAmount = 9990;

        /** Orden de compra de la transacción que se requiere anular */
        $buyOrder = filter_input(INPUT_POST, 'buyOrder');

        /** Monto que se desea anular de la transacción */
        $nullifyAmount = 9990;

         $request = array(
            "authorizationCode" => $authorizationCode,
            "authorizedAmount" => $authorizedAmount,
            "buyOrder" => $buyOrder,
            "nullifyAmount" => $nullifyAmount,
            "commercecode" => $configuration->getCommerceCode(),
        );

        $result = $webpay->getNullifyTransaction()->nullify($authorizationCode, $authorizedAmount, $buyOrder, $nullifyAmount, $commercecode);

        /** Verificamos resultado  de transacción */
        if (!isset($result->authorizationCode)) {
            $message = "webpay no disponible";
        } else {
            $message = "Transaci&oacute;n Finalizada";
        }

        $next_page = '';

        break;
}

echo "<h2>Step: " . $tx_step . "</h2>";

if (!isset($result)) {

    $result = "Ocurri&oacute; un error al procesar tu solicitud";
    echo "<div style = 'background-color:lightgrey;'><h3>result</h3>$result;</div><br/><br/>";
    echo "<a href='../..'>&laquo; volver a index</a>";
    die;
}
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

        <input type="hidden" name="token_ws" value="<?php echo $token ?>">

        <input type="hidden" name="authorizationCode" value="<?php echo $authorizationCode ?>">
        <input type="hidden" name="queryId" value="<?php echo $idQueryShare ?>">
        <input type="hidden" name="buyOrder" value="<?php echo $buyOrder ?>">
        <input type="submit" value="<?php echo $button_name; ?>">
    </form>
<?php } ?>
<br>
<a href="../..">&laquo; volver a index</a>
