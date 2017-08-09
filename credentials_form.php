<?php
//require('includes/application_top.php');

require_once (dirname(__FILE__) . '/vendor/autoload.php');


$mail = filter_var( $_POST['mail'], FILTER_SANITIZE_EMAIL);
$pass = filter_var( $_POST['pass'], FILTER_SANITIZE_STRING); 
$mode = filter_var( $_POST['mode'], FILTER_SANITIZE_STRING);


// instancio User 
use TodoPago\Data\User as User;
$user = new User($mail, $pass);

// test : 'http://127.0.0.1:8280/'
// prod : 'https://apis.todopago.com.ar/'
//obtengo elementos del formulario

use TodoPago\Sdk as Sdk;
$Sdk = new Sdk(array(),$mode);

try {
    $credentials = $Sdk->getCredentials($user);
} catch (Exception $e) {
    echo json_encode(array('error_message' => $e->getMessage()));
    exit;
}

list($partner, $apiKey) = explode(" ", $credentials->getApikey());

echo json_encode(array('merchantId'=> $credentials->getMerchant() , 'apiKey' => $apiKey, 'Authorization' => $credentials->getApikey() , 'ambiente' => $mode, 'error_message' => '0' ));

?>
