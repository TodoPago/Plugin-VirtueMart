<?php 
require('Commons.php');
class Services extends Commons {
	function getServicesFields($data, $customFieldsModel){

	$CSMDD28 = array();

	foreach($data->products as $prod){

		$customfields = $customFieldsModel->getCustomEmbeddedProductCustomFields($prod->virtuemart_product_id);

		foreach($customfields as $custom){
			if ($custom->custom_desc == 'CSMDD28'){
				$CSMDD28[] = trim(urlencode(htmlentities(strip_tags($custom->customfield_value))));
			}
		}
	}

	$fields = array(

                    	'CSMDD28'=>implode('#',$CSMDD28), //Tipo de Servicio. MANDATORIO. Valores posibles: Luz, Gas, Telefono, Agua, TV, Cable, Internet, Impuestos.		
                    	);



	return $fields;
}

}