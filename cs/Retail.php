<?php
require('Commons.php');
class retail extends Commons{

	public  function getFields($cart, $customFieldsModel){



		$fields = array(

                    	'CSSTCITY'=> $this->_sanitize_string($cart->BT['city']), //Ciudad de enví­o de la orden. MANDATORIO.		



                    	'CSSTEMAIL'=> $cart->BT['email'], //Mail del destinatario, MANDATORIO.		

                    	'CSSTFIRSTNAME'=> $this->_sanitize_string($cart->BT['first_name']), //Nombre del destinatario. MANDATORIO.		

                    	'CSSTLASTNAME'=> $this->_sanitize_string($cart->BT['last_name']), //Apellido del destinatario. MANDATORIO.		

                    	'CSSTPHONENUMBER'=> $this->_sanitize_string($cart->BT['phone_1']),//Número de teléfono del destinatario. MANDATORIO.		

                    	'CSSTPOSTALCODE'=> $cart->BT['zip'],//Código postal del domicilio de envío. MANDATORIO.		

                    	'CSSTSTREET1'=> $this->_sanitize_string($cart->BT['address_1']), //Domicilio de envío. MANDATORIO.				

                    	//Retail: datos a enviar por cada producto, los valores deben estar separado con #:		

                    	);



		return $fields; 

	}


}