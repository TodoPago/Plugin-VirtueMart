<?php
class TPConnector {
	function createTPConnector($method){

		if ($method->tp_ambiente == "test"){

			$todoPagoParams = '{"Authorize":"'.JURI::base()."/plugins/vmpayment/todopago/Authorize.wsdl".'", "Operations":"https://developers.todopago.com.ar/services/t/1.1/Operations?wsdl"}';
			$end_point = 'https://developers.todopago.com.ar/services/t/1.1/';
			$security =  $method->tp_security_code_test;
			$merchant = $method->tp_id_site_test;
		}
		else{

			$todoPagoParams = '{"Authorize":"'.JURI::base()."/plugins/vmpayment/todopago/Authorize.wsdl".'", "Operations":"https://developers.todopago.com.ar/services/t/1.1/Operations?wsdl"}';
			$end_point = 'https://apis.todopago.com.ar/services/t/1.1/';
			$security =  $method->tp_security_code_prod;
			$merchant = $method->tp_id_site_prod;
		}

		if(json_decode($method->tp_auth_http)==null){
			$auth = array("Authorization"=>$method->tp_auth_http);
		}else{
			$auth = json_decode($method->tp_auth_http, 1);
		}
		
		$todoPagoParams = json_decode($todoPagoParams, 1);

		$wsdl = array();
		$wsdl['Authorize'] = $todoPagoParams['Authorize'];
        //$wsdl['PaymentMethods'] = $todoPagoParams['PaymentMethods'];
		$wsdl['Operations'] = $todoPagoParams['Operations'];

		$http_header = array('Authorization'=>  $auth['Authorization']);


		$config = array_merge($wsdl, $auth ,$http_header );
		$config[] = $end_point;
		$config[] = $merchant;
		$config[] = $security;

		$config_validation = true;

		foreach($config as $c){

			if (empty($c)){
				$config_validation =  false;
			}
		}

		if ($config_validation){

			$connector = new TodoPago($http_header, $wsdl, $end_point);
		}
		else{
			echo "FALTA CONFIGURAR PLUGIN TODOPAGO";

		}


		$return = array('connector'=>$connector, 'merchant'=>$merchant, 'security'=>$security);

		return $return;
	}
}