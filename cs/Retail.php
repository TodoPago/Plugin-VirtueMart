<?php
require('Commons.php');
class retail extends Commons{

	public  function getFields($cart, $customFieldsModel){



		$fields = array(

                    	'CSSTCITY'=> (isset($cart->ST['city']))? $this->_sanitize_string($cart->ST['city']):$this->_sanitize_string($cart->BT['city']), //Ciudad de enví­o de la orden. MANDATORIO.
                    	'CSSTEMAIL'=> (isset($cart->ST['email']))? $cart->ST['email']:$cart->BT['email'], //Mail del destinatario, MANDATORIO.		

                    	'CSSTFIRSTNAME'=> (isset($cart->ST['first_name']))? $this->_sanitize_string($cart->ST['first_name']):$this->_sanitize_string($cart->BT['first_name']), //Nombre del destinatario. MANDATORIO.		

                    	'CSSTLASTNAME'=> (isset($cart->ST['last_name']))? $this->_sanitize_string($cart->ST['last_name']) :$this->_sanitize_string($cart->BT['last_name']), //Apellido del destinatario. MANDATORIO.		

                    	'CSSTPHONENUMBER'=> (isset($cart->ST['phone_1']))? $this->_sanitize_string($cart->ST['phone_1']):$this->_sanitize_string($cart->BT['phone_1']),//Número de teléfono del destinatario. MANDATORIO.		

                    	'CSSTPOSTALCODE'=> (isset($cart->ST['zip']))? $cart->ST['zip']:$cart->BT['zip'],//Código postal del domicilio de envío. MANDATORIO.		

                    	'CSSTSTREET1'=> (isset($cart->ST['address_1']))? $this->_sanitize_string($cart->ST['address_1']):$this->_sanitize_string($cart->BT['address_1']), //Domicilio de envío. MANDATORIO.				

                    	//Retail: datos a enviar por cada producto, los valores deben estar separado con #:		

                    	);



		return $fields; 

	}


}