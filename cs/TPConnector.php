<?php
use TodoPago\Sdk as Sdk;

require_once (dirname(__FILE__) . '/../vendor/autoload.php');

class TPConnector {
	function createTPConnector($method){
		
		$mode = $method->tp_ambiente;
		$security = $method->{tp_security_code_.$mode};
		$merchant = $method->{tp_id_site_.$mode};
		$auth = (json_decode($method->{tp_auth_http_.$mode})==null)? array("Authorization"=>$method->{tp_auth_http_.$mode}):$auth = json_decode($method->{tp_auth_http_.$mode}, 1);

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

			$connector = new Sdk($http_header, $mode);
		}
		else{
			return null;
		}

		$return = array('connector'=>$connector, 'merchant'=>$merchant, 'security'=>$security);
		
		return $return;
	}
}