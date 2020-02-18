<?php
require_once '../vendor/autoload.php';

use Transbank\Webpay\Configuration;
use Transbank\Webpay\Webpay;
?>
<h1>Ejemplos Webpay - Transaccion Mall Normal</h1>

<?php

/** Configuracion parametros de la clase Webpay */
$sample_baseurl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$configuration = Configuration::forTestingWebpayPlusMall();

/** Creacion Objeto Webpay */
$webpay = new Webpay($configuration);

$action = isset($_GET["action"]) ? $_GET["action"] : 'init';

$post_array = false;

switch ($action) {

    default:
        $tx_step = "Init";

        //$stores = array();

        $buyOrder  = rand(); // (Obligatorio) Es el código único de la orden de compra generada por el comercio mall.
        $sessionId = uniqid(); // (Opcional) Identificador de sesión, uso interno de comercio.
        $urlReturn = $sample_baseurl."?action=getResult"; // URL Retorno
        $urlFinal  = $sample_baseurl."?action=end"; // URL Final

        $stores = [
            [
                "storeCode" => "597044444402",
                "amount" => 1200,
                "buyOrder" => rand()
            ],
            [
                "storeCode" => "597044444403",
                "amount" => 2500,
                "buyOrder" => rand()
            ]
        ];

        $request = array(
            "buyOrder"  => $buyOrder,
            "sessionId" => $sessionId,
            "urlReturn" => $urlReturn,
            "urlFinal"  => $urlFinal,
            "stores"  => $stores,
        );

        /** Iniciamos Transaccion */
        $result = $webpay->getMallNormalTransaction()->initTransaction($buyOrder, $sessionId, $urlReturn, $urlFinal, $stores);

        /** Verificamos respuesta de inicio en webpay */
        if (!empty($result->token) && isset($result->token)) {
            $message = "Sesion iniciada con exito en Webpay";
            $token = $result->token;
            $next_page = $result->url;
        } else {
            $message = "webpay no disponible";
        }

        $button_name = "Continuar &raquo;";

        break;

    case "getResult":
        $tx_step = "Get Result";

        if (!isset($_POST["token_ws"]))
            break;

        $token = filter_input(INPUT_POST, 'token_ws');

        $request = array(
            "token" => filter_input(INPUT_POST, 'token_ws')
        );

        /** Rescatamos resultado y datos de la transaccion */
        $result = $webpay->getMallNormalTransaction()->getTransactionResult($token);

        $error = false;

        /** Se revisa si alguno de los comercios presenta transacción rechazada o errores */
        foreach ($result->detailOutput as $array_responses) {
            $resultCode = $array_responses->responseCode;
            if ($resultCode != 0) {
                $error = true;
            }
        }

        /** Verificamos resultado del pago */
        if (!$error) {
            $message = "Pago ACEPTADO por webpay (se deben guarar datos para mostrar voucher)";
            $next_page = $result->urlRedirection;
            $next_page_title = "Finalizar Pago";

            /** propiedad de HTML5 (web storage), que permite almacenar datos en nuestro navegador web */
            echo '<script>window.localStorage.clear();</script>';
            echo '<script>localStorage.setItem("commerceCode", '.$result->detailOutput["0"]->commerceCode.')</script>';
            echo '<script>localStorage.setItem("authorizationCode", '.$result->detailOutput["0"]->authorizationCode.')</script>';
            echo '<script>localStorage.setItem("amount", '.$result->detailOutput["0"]->amount.')</script>';
            echo '<script>localStorage.setItem("buyOrder", '.$result->detailOutput["0"]->buyOrder.')</script>';

        } else {
            $message = "Pago RECHAZADO por webpay en uno o mas comercios";
            $next_page = '';
        }

        $button_name = "Continuar &raquo;";
        break;

    case "end":

        $post_array = true;

        $tx_step = "End";
        $request = '';
        $result = $_POST;
        $message = "Transacion Finalizada";
        $next_page = $sample_baseurl."?action=nullify";
        $button_name = "Anular Transacci&oacute;n &raquo;";
        break;

    case "nullify":

        $tx_step = "nullify";

        /** Codigo de Comercio */
        $commercecode = filter_input(INPUT_POST, 'commerceCode');

        /** Código de autorización de la transacción que se requiere anular */
        $authorizationCode = filter_input(INPUT_POST, 'authorizationCode');

        /** Monto autorizado de la transacción que se requiere anular */
        $authorizedAmount = filter_input(INPUT_POST, 'amount');

        /** Orden de compra de la transacción que se requiere anular */
        $buyOrder = filter_input(INPUT_POST, 'buyOrder');

        /** Monto que se desea anular de la transacción */
        $nullifyAmount = 200;

         $request = array(
            "authorizationCode" => $authorizationCode,
            "authorizedAmount" => $authorizedAmount,
            "buyOrder" => $buyOrder,
            "nullifyAmount" => $nullifyAmount,
            "commercecode" => $commercecode,
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
    echo "<div style = 'background-color:lightgrey;'><h3>result</h3>$result</div><br/><br/>";
    echo "<a href='../..'>&laquo; volver a index</a>";
    die;
}

?>

<div style="background-color:lightyellow;">
	<h3>request</h3>
	<?php  var_dump($request); ?>
</div>
<div style="background-color:lightgrey;">
	<h3>result</h3>
	<?php  var_dump($result); ?>
</div>
<p><samp><?php  echo $message; ?></samp></p>
<?php if (strlen($next_page) && $post_array) { ?>

        <form action="<?php echo $next_page; ?>" method="post">
            <input type="hidden" name="commerceCode" id="commerceCode" value="">
            <input type="hidden" name="authorizationCode" id="authorizationCode" value="">
            <input type="hidden" name="amount" id="amount" value="">
            <input type="hidden" name="buyOrder" id="buyOrder" value="">
            <input type="submit" value="<?php echo $button_name; ?>">
        </form>

        <script>

            var commerceCode = localStorage.getItem('commerceCode');
            document.getElementById("commerceCode").value = commerceCode;

            var authorizationCode = localStorage.getItem('authorizationCode');
            document.getElementById("authorizationCode").value = authorizationCode;

            var amount = localStorage.getItem('amount');
            document.getElementById("amount").value = amount;

            var buyOrder = localStorage.getItem('buyOrder');
            document.getElementById("buyOrder").value = buyOrder;

            localStorage.clear();

        </script>

<?php } elseif (strlen($next_page)) { ?>
    <form action="<?php echo $next_page; ?>" method="post">

    <input type="hidden" name="token_ws" value="<?php echo ($token); ?>">
    <input type="submit" value="<?php echo $button_name; ?>">
</form>
<?php } ?>
<br>
<a href="../..">&laquo; volver a index</a>
