<?php
class TPConnector {
	function createTPConnector($method){
		
		if ($method->tp_ambiente == "test"){

			$security =  $method->tp_security_code_test;
			$merchant = $method->tp_id_site_test;
		}
		else{

			$security =  $method->tp_security_code_prod;
			$merchant = $method->tp_id_site_prod;
		}

		if(json_decode($method->tp_auth_http)==null){
			$auth = array("Authorization"=>$method->tp_auth_http);
		}else{
			$auth = json_decode($method->tp_auth_http, 1);
		}
		
		$http_header = array('Authorization'=>  $auth['Authorization']);
		
		$config[] = $http_header;
		$config[] = $merchant;
		$config[] = $security;

		$config_validation = true;

		foreach($config as $c){

			if (empty($c)){
				$config_validation =  false;
			}
		}

		if ($config_validation){

			$connector = new Sdk($http_header, $method->tp_ambiente);
		}
		else{
			return null;
		}

		$return = array('connector'=>$connector, 'merchant'=>$merchant, 'security'=>$security);
		
		return $return;
	}
}