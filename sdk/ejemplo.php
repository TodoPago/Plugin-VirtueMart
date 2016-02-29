<?php
//importo archivo con SDK
include_once dirname(__FILE__)."/Sdk.php";
	
//común a todas los métodos
$http_header = array('Authorization'=>'TODOPAGO 3e4e3f553c4c44ca84082e67fb564d33',
 'user_agent' => 'PHPSoapClient');
 	
$operationid = rand(1,10000000);
echo "<h3>$operationid</h3>\n";
//opciones para el método sendAuthorizeRequest
$optionsSAR_comercio = array (
	'Security'=>'3e4e3f553c4c44ca84082e67fb564d33',
	'EncodingMethod'=>'XML',
	'Merchant'=>35,
	'URL_OK'=>"http://localhost/exito.php?operationid=$operationid",
	'URL_ERROR'=>"http://localhost/error.php?operationid=$operationid"
);

$optionsSAR_operacion = array (
  	
	'MERCHANT'=> "35",
	'OPERATIONID'=>$operationid,
	'CURRENCYCODE'=> 032,
	'AMOUNT'=>"1",
	//Datos ejemplos CS
	'CSBTCITY'=> "Lanus",
	'CSSTCITY'=> "Lanus",
	
	'CSBTCOUNTRY'=> "AR",
	'CSSTCOUNTRY'=> "AR",
	
	'CSBTEMAIL'=> "javier.gutierrez@gmail.com",
	'CSSTEMAIL'=> "javier.gutierrez@gmail.com",
	
	'CSBTFIRSTNAME'=> "javier",
	'CSSTFIRSTNAME'=> "javier",      
	
	'CSBTLASTNAME'=> "Gutierrez",
	'CSSTLASTNAME'=> "Gutierrez",
	
	'CSBTPHONENUMBER'=> "541155893766",     
	'CSSTPHONENUMBER'=> "541155893766",     
	
	'CSBTPOSTALCODE'=> " 1414",
	'CSSTPOSTALCODE'=> " 1414",
	
	'CSBTSTATE'=> "B",
	'CSSTSTATE'=> "B",
	
	'CSBTSTREET1'=> "Cordero 740",
	'CSSTSTREET1'=> "Cordero 740",
	
	'CSBTCUSTOMERID'=> "453458",
	'CSBTIPADDRESS'=> "192.0.0.4",       
	'CSPTCURRENCY'=> "ARS",
	'CSPTGRANDTOTALAMOUNT'=> "1.00",
	'CSMDD7'=> "",     
	'CSMDD8'=> "Y",       
	'CSMDD9'=> "",       
	'CSMDD10'=> "",      
	'CSMDD11'=> "",
	'CSMDD12'=> "",     
	'CSMDD13'=> "",
	'CSMDD14'=> "",
	'CSMDD15'=> "",        
	'CSMDD16'=> "",
	'CSITPRODUCTCODE'=> "electronic_good",
	'CSITPRODUCTDESCRIPTION'=> "NOTEBOOK L845 SP4304LA DF TOSHIBA",     
	'CSITPRODUCTNAME'=> "NOTEBOOK L845 SP4304LA DF TOSHIBA",  
	'CSITPRODUCTSKU'=> "LEVJNSL36GN",
	'CSITTOTALAMOUNT'=> "1.00",
	'CSITQUANTITY'=> "1",
	'CSITUNITPRICE'=> "1.00"
	);

//opciones para el método getAuthorizeAnswer
$optionsGAA = array(
	'Security' => '3e4e3f553c4c44ca84082e67fb564d33',
	'Merchant' => "35",
	'RequestKey' => 'b5b16eb5-6276-dba9-03c1-4e682214cced',
	'AnswerKey' => '09b99f63-d3e2-9fc6-070c-efe5bb13aa80'
	);
	
//opciones para el método getAllPaymentMethods
$optionsGAMP = array("MERCHANT"=>35);
	
//opciones para el método getStatus 
$optionsGS = array('MERCHANT'=>35, 'OPERATIONID'=>"3407759");
	
//creo instancia de la clase TodoPago
$connector = new Sdk($http_header, "prod");
	
//ejecuto los métodos
$rta = $connector->sendAuthorizeRequest($optionsSAR_comercio, $optionsSAR_operacion);
//$rta2 = $connector->getAuthorizeAnswer($optionsGAA);
//$rta3 = $connector->getStatus($optionsGS);
//$rta4 = $connector->getAllPaymentMethods($optionsGAMP);
//$rta5= $connector->discoverPaymentMethods();

//Print values
echo "<h3>var_dump de la respuesta de Send Authorize Request</h3>";
var_dump($rta);
echo "<h3>var_dump de la respuesta de Get Authorize Answer</h3>";
//var_dump($rta2);
echo "<h3>var_dump de la respuesta de Get Status</h3>";
//var_dump($rta3);
echo "<h3>var_dump de la respuesta de GetAllPaymentMethods</h3>";
//var_dump($rta4);
echo "<h3>var_dump de la respuesta de discoverPaymentMethods</h3>";
//var_dump($rta5);
