<?php 
require('Commons.php');
class Ticketing extends Commons{
	public function getTicketingFields($data, $customFieldsModel){

		$CSMDD33 = array();
		$CSMDD34 = array();

		foreach($data->products as $prod){

			$customfields = $customFieldsModel->getCustomEmbeddedProductCustomFields($prod->virtuemart_product_id);

			foreach($customfields as $custom){
				if ($custom->custom_desc == 'CSMDD33'){
					$CSMDD33[] = trim(urlencode(htmlentities(strip_tags($custom->customfield_value))));
				}
				if ($custom->custom_desc == 'CSMDD34'){
					$CSMDD34[] = trim(urlencode(htmlentities(strip_tags($custom->customfield_value))));
				}
			}
		}


		$fields = array(		

                	'CSMDD33'=>implode('#',$CSMDD33), //Tipo de delivery. MANDATORIO. Valores posibles: WEB Session, Email, SmartPhone		
                	'CSMDD34'=>implode('#',$CSMDD34)
                	);

		return $fields;	
	}

}