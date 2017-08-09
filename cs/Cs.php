<?php
abstract class Cs {

	protected $order;
	private $customer;

	public function __construct($cart, $customFieldsModel){
		$this->customFieldsModel = $customFieldsModel;
		$this->cart = $cart;
		//Mage::log("constructor del CS: ".$this->order->getCustomerEmail());
	}

	public function getDataCS(){
		$datosCS = $this->completeCS();
		return array_merge($datosCS, $this->completeCSVertical());
	}

	//aca van los commonds fields
	private function completeCS(){
		$payDataOperacion = array();
		$billingAdress = $this->order->getBillingAddress();
		Mage::log("CSBTCITY - Ciudad de facturaci�n");
		$payDataOperacion ['CSBTCITY'] = $this->getField($billingAdress->getCity());
		Mage::log(" CSBTCOUNTRY - pa�s de facturaci�n (ver si magento utiliza C�digo ISO)");
		$payDataOperacion ['CSBTCOUNTRY'] = substr($this->getField($billingAdress->getCountry()),0,2);
		Mage::log(" CSBTCUSTOMERID - identificador del usuario (no correo electronico)");
		$payDataOperacion ['CSBTCUSTOMERID'] = $this->getField($billingAdress->getCustomerId());

		if($payDataOperacion ['CSBTCUSTOMERID']=="" or $payDataOperacion ['CSBTCUSTOMERID']==null)
		{
			$payDataOperacion ['CSBTCUSTOMERID']= "guest".date("ymdhs");
		}
		Mage::log(" CSBTIPADDRESS - ip de la pc del comprador");
		$payDataOperacion ['CSBTIPADDRESS'] = ($this->get_the_user_ip() == '::1') ? '127.0.0.1' : $this->get_the_user_ip();
		Mage::log(" CSBTEMAIL - email del usuario al que se le emite la factura");
		$payDataOperacion ['CSBTEMAIL'] = $this->getField($billingAdress->getEmail());
		Mage::log(" CSBTFIRSTNAME - nombre de usuario el que se le emite la factura");
		$payDataOperacion ['CSBTFIRSTNAME'] = $this->getField($billingAdress->getFirstname());
		Mage::log(" CSBTLASTNAME - Apellido del usuario al que se le emite la factura");
		$payDataOperacion ['CSBTLASTNAME'] = $this->getField($billingAdress->getLastname());
		Mage::log(" CSBTPOSTALCODE - Código Postal de la dirección de facturación");
		$payDataOperacion ['CSBTPOSTALCODE'] = $this->getField($billingAdress->getPostcode());
		Mage::log(" CSBTPHONENUMBER - Tel�fono del usuario al que se le emite la factura. No utilizar guiones, puntos o espacios. Incluir c�digo de pa�s");
		$payDataOperacion ['CSBTPHONENUMBER'] = $this->getField($billingAdress->getTelephone());
		
		$payDataOperacion ['CSBTSTATE'] =  strtoupper(substr($this->getField($billingAdress->getRegion()),0,1));
		Mage::log(" CSBTSTREET1 - Domicilio de facturaci�n (calle y nro)");
		$payDataOperacion ['CSBTSTREET1'] = $this->getField($billingAdress->getStreet1());
		Mage::log(" CSBTSTREET2 - Complemento del domicilio. (piso, departamento)_ No Mandatorio");
		$payDataOperacion ['CSBTSTREET2'] = $this->getField($billingAdress->getStreet2());
		Mage::log(" CSPTCURRENCY- moneda");
		$payDataOperacion ['CSPTCURRENCY'] = $this->getField($this->order->getBaseCurrencyCode());
		Mage::log(" CSPTGRANDTOTALAMOUNT - 999999[.CC] Con decimales opcional usando el puntos como separador de decimales. No se permiten comas, ni como separador de miles ni como separador de decimales.");
		$payDataOperacion ['CSPTGRANDTOTALAMOUNT'] = number_format($this->order->getGrandTotal(), 2, ".", "");
		Mage::log(" CSMDD6 - Canal de venta");
		$payDataOperacion ['CSMDD6'] = Mage::getStoreConfig('payment/modulodepago2/cs_canaldeventa');
		Mage::log(" CSMDD7 - Fecha Registro Comprador (num Dias) - ver que pasa si es guest");
		$date = Mage::getModel('core/date');
		$fecha_1 = date('d-m-Y', $date->timestamp($this->customer->getCreatedAt()));
		$fecha_2 = date('d-m-Y', $date->timestamp(Mage::app()->getLocale()->date()));
		$payDataOperacion ['CSMDD7'] = Mage::helper('modulodepago2/data')->diasTranscurridos($fecha_1, $fecha_2);
		Mage::log(" CSMDD8 - Usuario Guest? (Y/N). En caso de ser Y, el campo CSMDD9 no deber� enviarse");
		if($this->order->getCustomerIsGuest()){
			$payDataOperacion ['CSMDD8'] = "N";
		} else{
			Mage::log(" CSMDD9 - Customer password Hash: criptograma asociado al password del comprador final");
			$payDataOperacion ['CSMDD9'] = $this->customer->getPasswordHash();
		}

		Mage::log(" CSMDD11 Customer Cell Phone");
		if(!$this->customer->getCelular()){
			$payDataOperacion ['CSMDD11'] = $payDataOperacion['CSBTPHONENUMBER'];
		} else{
			$payDataOperacion ['CSMDD11'] = $this->getField($this->customer->getCelular());
		}

		return $payDataOperacion;

	}

	private function _sanitize_string($string){
		$string = htmlspecialchars_decode($string);

		$re = "/\\[(.*?)\\]|<(.*?)\\>/i";
		$subst = "";
		$string = preg_replace($re, $subst, $string);

		$replace = array("!","'","\'","\"","  ","$","\\","\n","\r",
			'\n','\r','\t',"\t","\n\r",'\n\r','&nbsp;','&ntilde;',".,",",.","+", "%", "-", ")", "(", "°");
		$string = str_replace($replace, '', $string);

		$cods = array('\u00c1','\u00e1','\u00c9','\u00e9','\u00cd','\u00ed','\u00d3','\u00f3','\u00da','\u00fa','\u00dc','\u00fc','\u00d1','\u00f1');
		$susts = array('Á','á','É','é','Í','í','Ó','ó','Ú','ú','Ü','ü','Ṅ','ñ');
		$string = str_replace($cods, $susts, $string);

		$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
		$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
		$string = str_replace($no_permitidas, $permitidas ,$string);

		return $string;
	}

	protected function getMultipleProductsInfo(){
		$payDataOperacion = array();
		Mage::log("CSITPRODUCTCODE C�digo del producto");
        //$id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        //$order = Mage::getModel('sales/order')->load($id);
        //$productos = $order->getAllItems();
		$productos = $this->order->getItemsCollection();
        //var_dump($productos);
        ///datos de la orden separados con #
		$productcode_array = array();
		$description_array = array();
		$name_array = array();
		$sku_array = array();
		$totalamount_array = array();
		$quantity_array = array();
		$price_array = array();

		foreach($productos as $item){
			
			$product_id = Mage::getModel('catalog/product')->load($item->getProductId())->getTodopagocodigo();
			$productcode_array[] = $this->getCategoryArray($productId);

			$_description = Mage::getModel('catalog/product')->load($item->getProductId())->getDescription();
			$_description = $this->getField($_description);
			$_description = trim($_description);
			$_description = substr($_description, 0,15);
			$description_array [] = str_replace("#","",$_description);

			$product_name = $item->getName();
			$name_array [] = $product_name;

			$sku = $item->getSku();
			$sku_array [] = $this->getField($sku);

			$product_quantity = $item->getQtyOrdered();
			$product_price = $item->getPrice();
			$product_amount = number_format($product_quantity * $product_price, 2, ".", "");
			$totalamount_array[] = $product_amount;

			$quantity_array [] = intval($product_quantity);

			$price_array [] = $product_price;

		}
		$payDataOperacion ['CSITPRODUCTCODE'] = join('#', $productcode_array);
		$payDataOperacion ['CSITPRODUCTDESCRIPTION'] = join("#", $description_array);
		$payDataOperacion ['CSITPRODUCTNAME'] = join("#", $name_array);
		$payDataOperacion ['CSITPRODUCTSKU'] = join("#", $sku_array);
		Mage::log("CSITTOTALAMOUNT - CSITTOTALAMOUNT = CSITUNITPRICE * CSITQUANTITY");
		$payDataOperacion ['CSITTOTALAMOUNT'] = join("#", $totalamount_array);
		$payDataOperacion ['CSITQUANTITY'] = join("#", $quantity_array);
		$payDataOperacion ['CSITUNITPRICE'] = join("#", $price_array);
		return $payDataOperacion;
	}

	public function getField($datasources){
		$return = "";
		try{

			$return = $this->_sanitize_string($datasources);
			Mage::log("devolvio $return");

		}catch(Exception $e){
			Mage::log("Modulo de pago - TodoPago ==> operation_id:  $this->order->getIncrementId() - 
				no se pudo agregar el campo: Exception: $e");
		}

		return $return;
	}


	public function get_the_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	protected abstract function getCategoryArray($productId);
	protected abstract function completeCSVertical();

}