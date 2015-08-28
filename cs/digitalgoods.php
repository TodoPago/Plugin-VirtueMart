<?php
require('commons.php');
class Digitalgoods extends Commons {

	function getDigitalGoodsFields($data, $customFieldsModel){

		$CSMDD31 = array();

		foreach($data->products as $prod){

			$customfields = $customFieldsModel->getCustomEmbeddedProductCustomFields($prod->virtuemart_product_id);

			foreach($customfields as $custom){
				if ($custom->custom_desc == 'CSMDD31'){
					$CSMDD31[] = trim(urlencode(htmlentities(strip_tags($custom->customfield_value))));
				}
			}
		}

		$fields = array(

                	'CSMDD31'=>implode('#',$CSMDD31), //Tipo de delivery. MANDATORIO. Valores posibles: WEB Session, Email, SmartPhone		
                	);

		return $fields;
	}

}