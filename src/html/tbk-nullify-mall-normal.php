<?php
require_once '../vendor/autoload.php';

use Transbank\Webpay\Configuration;
use Transbank\Webpay\Webpay;
?>
<h1>Ejemplos Webpay - Transaccion Mall Normal Anulaci&oacute;n</h1>
<?php

/* Configuracion parametros de la clase Webpay */
$sample_baseurl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$configuration = Configuration::forTestingWebpayPlusMall();

/* Creacion Objeto Webpay */
$webpay = new Webpay($configuration);

$action = isset($_GET["action"]) ? $_GET["action"] : 'init';

switch ($action) {

    default:

        $tx_step = "Init";
        $next_page = $sample_baseurl . "?action=nullify";

        echo "<h2>Step: " . $tx_step . "</h2>";

        ?>

                <form id="formulario" action="<?php echo $next_page; ?>" method="post">
                    <fieldset>
                        <legend>Formulario de Anulaci&oacute;n</legend><br/><br/>
                            <label>CommerceCode:</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <input id="commercecode" name="commercecode" type="text" />&nbsp;&nbsp;&nbsp;<br/><br/><br/>
                            <label>authorizationCode:</label>
                                <input id="authorizationCode" name="authorizationCode" type="text" />&nbsp;&nbsp;&nbsp;
                            <label>authorizedAmount:</label>
                                <input id="authorizedAmount" name="authorizedAmount" type="text" />&nbsp;&nbsp;&nbsp;
                            <label>buyOrder:</label>
                                <input id="buyOrder" name="buyOrder" type="text" />&nbsp;&nbsp;&nbsp;
                            <label>nullifyAmount:</label>
                                <input id="nullifyAmount" name="nullifyAmount" type="text" /><br/><br/><br/>
                            <input id="campo3" name="enviar" type="submit" value="Enviar" />
                    </fieldset>
                </form>
                <a href="..">&laquo; volver a index</a>
        <?php

        die;

    case "nullify":

        $tx_step = "nullify";

        /** Codigo de Comercio */
        $commercecode = filter_input(INPUT_POST, 'commercecode');

        /** Código de autorización de la transacción que se requiere anular */
        $authorizationCode = filter_input(INPUT_POST, 'authorizationCode');

        /** Monto autorizado de la transacción que se requiere anular */
        $authorizedAmount = filter_input(INPUT_POST, 'authorizedAmount');

        /** Orden de compra de la transacción que se requiere anular */
        $buyOrder = filter_input(INPUT_POST, 'buyOrder');

        /** Monto que se desea anular de la transacción */
        $nullifyAmount = filter_input(INPUT_POST, 'nullifyAmount');

        $request = array(
            "authorizationCode" => $authorizationCode, // Código de autorización
            "authorizedAmount" => $authorizedAmount, // Monto autorizado
            "buyOrder" => $buyOrder, // Orden de compra
            "nullifyAmount" => $nullifyAmount, // idsession local
            "commercecode" => $commercecode,
        );

        /** Iniciamos Transaccion */
        $result = $webpay->getNullifyTransaction()->nullify($authorizationCode, $authorizedAmount, $buyOrder, $nullifyAmount, $commercecode);

         if (isset($result->authorizationCode)) {
            $message = "Transacci&oacute;n anulada con exito en Webpay";
            $next_page = "";
          } else {
            $message = "webpay no disponible";
            $next_page = "";

          }

        break;
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
<?php if (strlen($next_page)) { ?>
<form action="<?php echo $next_page; ?>" method="post">
	<input type="hidden" name="token_ws" value="<?php echo $token; ?>">
	<input type="submit" value="Continuar &raquo;">
</form>
<?php } ?>
<br>
<a href="..">&laquo; volver a index</a>
