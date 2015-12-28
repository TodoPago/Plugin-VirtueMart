<?php

/*
* @author todopago.
* @version $Id: todopago.php 7487 2013-12-17 15:03:42Z alatak $
* @package VirtueMart
* @subpackage payment
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.org
*/

defined ('_JEXEC') or die('Restricted access');

if (!class_exists ('vmPSPlugin')) {
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}

require('sdk/todopago.php');
require ('cs/helpers.php');

class plgVmpaymentTodopago extends vmPSPlugin {

    private $tp_states = '';

    function __construct (& $subject, $config) {

        parent::__construct ($subject, $config);

        // unique filelanguage for all TODOPAGO methods

        $jlang = JFactory::getLanguage ();
        $jlang->load ('plg_vmpayment_todopago', JPATH_ADMINISTRATOR, NULL, TRUE);
        $this->_loggable = TRUE;
        $this->_debug = TRUE;

        $this->tableFields = array_keys (Helper::getTableSQLFields ());
        $this->_tablepkey = 'id'; //virtuemart_TODOPAGO_id';
        $this->_tableId = 'id'; //'virtuemart_TODOPAGO_id';

        $varsToPush = array(
            'tp_vertical_type'    => array('', 'char'),
            'tp_canal_ingreso'    => array('', 'char'),
            'tp_endpoint_test' => array('', 'int'),
            'tp_wsdl_test'   => array('', 'int'),
            'tp_auth_http'          => array('', 'int'),
            'tp_dead_line'          => array('', 'int'),
            'tp_id_site_test'              => array('', 'int'),
            'tp_security_code_test'       => array('', 'char'),
            'tp_endpoint_prod'           =>  array('', 'char'),
            'tp_wsdl_prod'               =>  array('', 'char'),
            'tp_id_site_prod'            =>  array('', 'char'),
            'tp_security_code_prod'      =>  array('', 'char'),
            'tp_order_status_init'       =>  array('', 'char'),
            'tp_order_status_aproved'    =>  array('', 'char'),
            'tp_order_status_rejected'    =>  array('', 'char'),
            'tp_order_status_offline'   =>  array('', 'char'),
            'tp_ambiente'      =>  array('', 'char'),
            'todopago_medios_de_pago'=> array('', 'char'),
        );

        $this->setConfigParameterable ($this->_configTableFieldName, $varsToPush);
    }




    function getTableSQLFields () {
        $SQLfields = array('id'                     => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'virtuemart_order_id'    => 'int(1) UNSIGNED',
            'order_number'           => ' char(64)',
            'virtuemart_paymentmethod_id' => 'mediumint(1) UNSIGNED',
            'payment_name'            => 'varchar(5000)',
            'payment_order_total'     => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\'',
            'payment_currency'        => 'char(3) ',
            'cost_per_transaction'    => 'decimal(10,2)',
            'cost_percent_total'      => 'decimal(10,2)',
            'tax_id'                  => 'smallint(1)',
            'security_code'                  => 'varchar(100)',
            'user_session'            => 'varchar(255)',
            // status report data returned by TODOPAGO to the merchant
            'mb_pay_to_email'         => 'varchar(50)',
            'mb_pay_from_email'       => 'varchar(50)',
            'mb_merchant_id'          => 'int(10) UNSIGNED',
            'mb_transaction_id'       => 'varchar(15)',
            'mb_rec_payment_id'       => 'int(10) UNSIGNED',
            'mb_rec_payment_type'     => 'varchar(16)',
            'mb_amount'               => 'decimal(19,2)',
            'mb_currency'             => 'char(3)',
            'mb_status'               => 'tinyint(1)',
            'mb_md5sig'               => 'char(32)',
            'mb_sha2sig'              => 'char(64)',
            'mbresponse_raw'          => 'varchar(512)',
                           // AMBIENTE PRODUCCION
            'tp_vertical_type'    => 'varchar(100)',
            'tp_canal_ingreso'    => 'varchar(100)',
            'tp_endpoint_test' => 'varchar(100)',
            'tp_wsdl_test'   => 'varchar(100)',
            'tp_auth_http'          => 'varchar(100)',
            'tp_dead_line'          => 'varchar(100)',
            'tp_id_site_test'              => 'varchar(100)',
            'tp_security_code_test'       => 'varchar(100)',
            'tp_endpoint_prod'           => 'varchar(100)',
            'tp_wsdl_prod'               => 'varchar(100)',
            'tp_id_site_prod'            => 'varchar(100)',
            'tp_security_code_prod'      => 'varchar(100)',
            'tp_order_status_init'      =>  'varchar(100)',
            'tp_order_status_aproved'    =>  'varchar(100)',
            'tp_order_status_rejected'   =>  'varchar(100)',
            'tp_order_status_offline'     => 'varchar(100)',
            'tp_security_code_prod'      =>  'varchar(100)',
            'tp_ambiente'      =>  'varchar(100)'
            );
return $SQLfields;
}

    public function getVmPluginCreateTableSQL () {

        return $this->createTableSQL ('Payment Todopago Table');
    }



    function _getTODOPAGOURL ($method) {

        $url = 'www.todopago.com';
        return $url;
    }



    function _processStatus (&$mb_data, $vmorder, $method) {


        switch ($mb_data['status']) {

            case 2 :

                $mb_data['payment_status'] = 'Completed';
                break;

            case 0 :

                $mb_data['payment_status'] = 'Pending';
                break;

            case -1 :

                $mb_data['payment_status'] = 'Cancelled';
                break;

            case -2 :

                $mb_data['payment_status'] = 'Failed';
                break;

            case -3 :
                $mb_data['payment_status'] = 'Chargeback';
                break;
        }



        $md5data = $mb_data['merchant_id'] . $mb_data['transaction_id'] .

            strtoupper (md5 (trim($method->secret_word))) . $mb_data['mb_amount'] . $mb_data['mb_currency'] .

            $mb_data['status'];
        $calcmd5 = md5 ($md5data);

        if (strcmp (strtoupper ($calcmd5), $mb_data['md5sig'])) {
            return "MD5 checksum doesn't match - calculated: $calcmd5, expected: " . $mb_data['md5sig'];
        }


        return FALSE;
    }


    function _getPaymentResponseHtml ($paymentTable, $payment_name) {

        VmConfig::loadJLang('com_virtuemart');

        $html = '<table>' . "\n";

        $html .= $this->getHtmlRow ('COM_VIRTUEMART_PAYMENT_NAME', $payment_name);

        if (!empty($paymentTable)) {

            $html .= $this->getHtmlRow ('TODOPAGO_ORDER_NUMBER', $paymentTable->order_number);

        }



        $html .= '</table>' . "\n";

        return $html;

    }


    function _getInternalData ($virtuemart_order_id, $order_number = '') {

        $db = JFactory::getDBO ();

        $q = 'SELECT * FROM `' . $this->_tablename . '` WHERE ';

        if ($order_number) {

            $q .= " `order_number` = '" . $order_number . "'";

        }

        else {

            $q .= ' `virtuemart_order_id` = ' . $virtuemart_order_id;

        }



        $db->setQuery ($q);



        if (!($paymentTable = $db->loadObject ())) {



            JError::raiseWarning(500, $db->getErrorMsg());

            return '';


        }


        return $paymentTable;

    }



    function _storeInternalData ($method, $mb_data, $virtuemart_order_id) {

        $db = JFactory::getDBO ();

        $query = 'SHOW COLUMNS FROM `' . $this->_tablename . '` ';


        $db->setQuery ($query);

        $columns = $db->loadColumn (0);

        $post_msg = '';


        foreach ($mb_data as $key => $value) {

            $post_msg .= $key . "=" . $value . "<br />";


            $table_key = 'mb_' . $key;



            if (in_array ($table_key, $columns)) {



                $response_fields[$table_key] = $value;

            }

        }



        $response_fields['payment_name'] = $this->renderPluginName ($method);

        $response_fields['mbresponse_raw'] = $post_msg;

        $response_fields['order_number'] = $mb_data['transaction_id'];

        $response_fields['virtuemart_order_id'] = $virtuemart_order_id;

        $this->storePSPluginInternalData ($response_fields, 'virtuemart_order_id', TRUE);

    }


    function _parse_response ($response) {

        $matches = array();
        $rlines = explode ("\r\n", $response);

        foreach ($rlines as $line) {

            if (preg_match ('/([^:]+): (.*)/im', $line, $matches)) {
                continue;
            }


            if (preg_match ('/([0-9a-f]{32})/im', $line, $matches)) {

                return $matches;
            }
        }
        return $matches;
    }


    function plgVmOnShowOrderPaymentBE($virtuemart_order_id, $paymethod_id){


    }

    function plgVmConfirmedOrder ($cart, $order) {

        if (!($method = $this->getVmPluginMethod ($order['details']['BT']->virtuemart_paymentmethod_id))) {
            return NULL; // Another method was selected, do nothing
        }

        if (!$this->selectedThisElement ($method->payment_element)) {
            return FALSE;
        }



        $session = JFactory::getSession ();
        $return_context = $session->getId ();
        $this->logInfo ('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');

        if (!class_exists ('VirtueMartModelOrders')) {

            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }



        if (!class_exists ('VirtueMartModelCurrency')) {

            require(VMPATH_ADMIN . DS . 'models' . DS . 'currency.php');
        }


        $usrBT = $order['details']['BT'];
        $address = ((isset($order['details']['ST'])) ? $order['details']['ST'] : $order['details']['BT']);

        if (!class_exists ('TableVendors')) {

            require(VMPATH_ADMIN . DS . 'tables' . DS . 'vendors.php');
        }


        $vendorModel = VmModel::getModel ('Vendor');
        $vendorModel->setId (1);
        $vendor = $vendorModel->getVendor ();
        $vendorModel->addImages ($vendor, 1);
        $this->getPaymentCurrency ($method);

        $q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="'.$method->payment_currency . '" ';
        $db = JFactory::getDBO ();

        $db->setQuery ($q);

        $currency_code_3 = $db->loadResult ();



        $totalInPaymentCurrency = vmPSPlugin::getAmountInCurrency($order['details']['BT']->order_total,$method->payment_currency);

        $cartCurrency = CurrencyDisplay::getInstance($cart->pricesCurrency);



        if ($totalInPaymentCurrency['value'] <= 0) {

            vmInfo (vmText::_ ('VMPAYMENT_TODOPAGO_PAYMENT_AMOUNT_INCORRECT'));
            return FALSE;
        }


        $lang = JFactory::getLanguage ();
        $tag = substr ($lang->get ('tag'), 0, 2);


        $post_variables = array();
        require_once ('cs/TPConnector.php');
        $tpconnector = new TPConnector();
        $connector_data = $tpconnector->createTPConnector($method);
        $this->logInfo("tpconnector".json_encode($connector_data), "message");        
        
        $connector = $connector_data['connector'];
        $security_code = $connector_data['security'];
        $merchant = $connector_data['merchant'];



        $return_url =  JURI::root().'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&on=' .

            $order['details']['BT']->order_number .

            '&pm=' .

            $order['details']['BT']->virtuemart_paymentmethod_id .

            '&Itemid=' . vRequest::getInt ('Itemid') .

                '&lang='.vRequest::getCmd('lang','');



        $cancel_url = JURI::root().'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&on=' .

            $order['details']['BT']->order_number .

            '&pm=' .

            $order['details']['BT']->virtuemart_paymentmethod_id .

            '&Itemid=' . vRequest::getInt ('Itemid') .
                '&lang='.vRequest::getCmd('lang','');


        $status_url  = JURI::root().'index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&tmpl=component&lang='.vRequest::getCmd('lang','');


        $optionsSAR_comercio = array (

            'Security' => $security_code,

            'EncodingMethod' => 'XML',

            'Merchant' => $merchant,
            'PUSHNOTIFYENDPOINT'=>
                    $return_url =  JURI::root().'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&on=' .

                        $order['details']['BT']->order_number .

                        '&pm=' .

                        $order['details']['BT']->virtuemart_paymentmethod_id,

            'URL_OK' => $return_url,

            'URL_ERROR' => $cancel_url

        );

        $customFieldsModel = VmModel::getModel ('Customfields');

        $optionsSAR_operacion = $this->getCommonFields($cart, $customFieldsModel, $this->tp_states);

        $currency_model = VmModel::getModel('currency');
        $currency = $currency_model->getCurrency($order['details']['BT']->user_currency_id);


        $countryIso = ShopFunctions::getCountryByID($order['details']['BT']->virtuemart_country_id,'country_2_code');
        $countryName = ShopFunctions::getCountryByID($order['details']['BT']->virtuemart_country_id);

        $extra_fields = array();

        require('cs/FactoryTodopago.php');
        $extra_fields = FactoryTodopago::get_extractor($method->tp_vertical_type, $cart, $customFieldsModel);

        $optionsSAR_operacion = array_merge($optionsSAR_operacion, $extra_fields);

        $optionsSAR_operacion['MERCHANT'] = $merchant;
        $optionsSAR_operacion['CURRENCYCODE'] = "032";
        $optionsSAR_operacion['CSPTCURRENCY'] = "ARS";
        $optionsSAR_operacion['OPERATIONID'] = $order['details']['BT']->order_number;
        $optionsSAR_operacion['CSBTCOUNTRY'] = $countryIso;
        $optionsSAR_operacion['CSMDD9'] = JFactory::getUser()->password;
        $optionsSAR_operacion['CSSTSTATE'] = $this->tp_states;
        $optionsSAR_operacion['CSSTCOUNTRY'] = $countryIso;
        $optionsSAR_operacion['CSMDD12'] = $method->tp_dead_line;
        $optionsSAR_operacion['CSMDD13'] = $this->_sanitize_string($cart->cartData['shipmentName']);

        $this->logInfo("TP - SARcomercio - ".json_encode($optionsSAR_comercio), "message");

        $this->logInfo("TP - SARoperacion - ".json_encode($optionsSAR_operacion), "message");

        $rta = $connector->sendAuthorizeRequest($optionsSAR_comercio, $optionsSAR_operacion);

        $this->logInfo("TP - SAR rta - ".json_encode($rta), "message");
        if($rta["StatusCode"] == 702){
            $this->logInfo("TP - SARoperacion - reintento SAR".json_encode($optionsSAR_operacion), "message");
            $rta = $connector->sendAuthorizeRequest($optionsSAR_comercio, $optionsSAR_operacion);
        }

        setcookie('RequestKey',$rta["RequestKey"],  time() + (86400 * 30), "/");

        $session = JFactory::getSession ();

        $return_context = $session->getId ();



        $dbValues['user_session'] = $return_context;

        $dbValues['order_number'] = $order['details']['BT']->order_number;

        $dbValues['payment_name'] = $this->renderPluginName ($method, $order);

        $dbValues['virtuemart_paymentmethod_id'] = $cart->virtuemart_paymentmethod_id;

        $dbValues['cost_per_transaction'] = $method->cost_per_transaction;

        $dbValues['cost_percent_total'] = $method->cost_percent_total;

        $dbValues['payment_currency'] = $method->payment_currency;

        $dbValues['payment_order_total'] = $totalInPaymentCurrency['value'];

        $dbValues['tax_id'] = $method->tax_id;

        $dbValues['security_code'] = $method->security_code;

        $this->storePSPluginInternalData ($dbValues);


        $cart->_confirmDone = TRUE;

        $cart->_dataValidated = TRUE;

        $cart->setCartIntoSession ();

        if ($rta['StatusCode']!= -1){
            echo "<script>alert('Su pago no puede ser procesado. Intente nuevamente más tarde')</script>";
            $this->logInfo("TP - Redirect to: ".$rta['URL_Request'], "message");
            echo "<script>window.location.href = '".JURI::root()."index.php/cart/'</script>";
        }else{
            $this->logInfo("TP - Redirect to: ".$rta['URL_Request'], "message");
            //echo "<script>window.location.href = '".$rta['URL_Request']."'</script>";
            header('Location: '.$rta['URL_Request']);
        }
    }

    function catIdToName($catid) {
        $db = JFactory::getDBO();

        return $row;
    }

    function getCommonFields($cart, $customFieldsModel, $tp_states=null){

        $CSITPRODUCTDESCRIPTION = array();
        $CSITPRODUCTNAME = array();
        $CSITPRODUCTSKU = array();
        $CSITTOTALAMOUNT = array();
        $CSITQUANTITY = array();
        $CSITUNITPRICE = array();
        $CSITPRODUCTCODE = array();

        if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

        foreach($cart->products as $prod){
            $categoryModel = VmModel::getModel('Category');
            $cat = $categoryModel->getCategory($prod->category, true);
            $cat_name = $cat->category_name;
            if ($cat_name=="" or $cat_name==null){
                $cat_name= "default";
            }
            $CSITPRODUCTCODE[] = $cat_name;

            $product_description = $this->_sanitize_string(trim(urlencode(htmlentities(strip_tags($prod->product_desc)))));
            $CSITPRODUCTDESCRIPTION[] = substr($product_description, 0, 10);
            $CSITPRODUCTNAME[] =  trim(urlencode(htmlentities(strip_tags($prod->product_name))));
            $CSITPRODUCTSKU[] = $prod->product_sku;
            $CSITTOTALAMOUNT[] = number_format(($prod->prices['salesPrice'] * $prod->amount),2,".", "");
            $CSITQUANTITY[] = intval($prod->amount);
            $CSITUNITPRICE[] = number_format($prod->prices['salesPrice'],2,".","");

            $customfields = $customFieldsModel->getCustomEmbeddedProductCustomFields($prod->virtuemart_product_id);
        }

        /** COMMON FIELDS **/

        $fields = array(

            'CSBTCITY'=>$cart->BT['city'], //Ciudad de facturación, MANDATORIO.

            'CSBTCUSTOMERID'=>$cart->user->customer_number, //Identificador del usuario al que se le emite la factura. MANDATORIO. No puede contener un correo electrónico.

            'CSBTIPADDRESS'=>"127.0.0.1",//Helper::getTodoPagoClientIp(), //IP de la PC del comprador. MANDATORIO.

            'CSBTEMAIL'=>$cart->BT['email'], //Mail del usuario al que se le emite la factura. MANDATORIO.

            'EMAILCLIENTE'=>$cart->BT['email'],

            'CSBTFIRSTNAME'=>$this->_sanitize_string($cart->BT['first_name']) ,//Nombre del usuario al que se le emite la factura. MANDATORIO.

            'CSBTLASTNAME'=>$this->_sanitize_string($cart->BT['last_name']), //Apellido del usuario al que se le emite la factura. MANDATORIO.

            'CSBTPHONENUMBER'=>$cart->BT['phone_1'],//, //Teléfono del usuario al que se le emite la factura. No utilizar guiones, puntos o espacios. Incluir código de país. MANDATORIO.

            'CSBTPOSTALCODE'=>$cart->BT['zip'], //Código Postal de la dirección de facturación. MANDATORIO.

            'CSBTSTATE'=>$tp_states, //Provincia de la dirección de facturación. MANDATORIO. Ver tabla anexa de provincias.

            'CSBTSTREET1'=>$this->_sanitize_string($cart->BT['address_1']), //Domicilio de facturación (calle y nro). MANDATORIO.

            'CSPTGRANDTOTALAMOUNT'=>number_format($cart->cartPrices['billTotal'],2,".", ""),

            'CSITPRODUCTCODE'=>implode('#',$CSITPRODUCTCODE),

            'CSITPRODUCTDESCRIPTION'=> implode('#',$CSITPRODUCTDESCRIPTION), //Descripción del producto. CONDICIONAL.

            'CSITPRODUCTNAME'=>implode('#',$CSITPRODUCTNAME), //Nombre del producto. CONDICIONAL.

            'CSITPRODUCTSKU'=>implode('#',$CSITPRODUCTSKU), //Código identificador del producto. CONDICIONAL.

            'CSITTOTALAMOUNT'=> implode('#',$CSITTOTALAMOUNT), //CSITTOTALAMOUNT=CSITUNITPRICE*CSITQUANTITY "999999[.CC]" Con decimales opcional usando el puntos como separador de decimales. No se permiten comas, ni como separador de miles ni como separador de decimales. CONDICIONAL.

            'CSITQUANTITY'=>implode('#',$CSITQUANTITY), //Cantidad del producto. CONDICIONAL.

            'CSITUNITPRICE'=>implode('#',$CSITUNITPRICE), //Formato Idem CSITTOTALAMOUNT. CONDICIONAL.

            'AMOUNT' => number_format($cart->cartPrices['billTotal'], 2, ".", "")


        );

        return $fields;


    }


    function plgVmgetPaymentCurrency ($virtuemart_paymentmethod_id, &$paymentCurrencyId) {


        if (!($method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id))) {



            return NULL;



        } // Another method was selected, do nothing


        if (!$this->selectedThisElement ($method->payment_element)) {



            return FALSE;



        }

        $this->getPaymentCurrency ($method);



        $paymentCurrencyId = $method->payment_currency;



    }


    function plgVmOnPaymentResponseReceived (&$html) {
        if(isset($_GET["push_notification"])){
              $method = $this->getVmPluginMethod ("1");

              $orderModel = VmModel::getModel('orders');


              $order['virtuemart_order_id'] = 29;
              $order['comments'] = "";

              $orderModel->updateStatusForOneOrder(29, $order, false);


              var_dump($method->virtuemart_paymentmethod_id);
              var_dump($orderModel);

            die();
        }

        $this->logInfo("Tp - VirtueMart vuelve a tomar en control (vuelve del formulario)", "message");
        if (!class_exists ('VirtueMartCart')) {



            require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');



        }



        if (!class_exists ('shopFunctionsF')) {



            require(VMPATH_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');



        }



        if (!class_exists ('VirtueMartModelOrders')) {



            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');



        }


        VmConfig::loadJLang('com_virtuemart_orders', TRUE);



        $mb_data = vRequest::getPost();



        // the payment itself should send the parameter needed.



        $virtuemart_paymentmethod_id = vRequest::getInt ('pm', 0);



        $order_number = vRequest::getString ('on', 0);



        if (!($method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id))) {



            return NULL;



        } // Another method was selected, do nothing







        if (!$this->selectedThisElement ($method->payment_element)) {



            return NULL;



        }



        if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber ($order_number))) {


            return NULL;


        }


        VmConfig::loadJLang('com_virtuemart');



        $orderModel = VmModel::getModel('orders');



        $order = $orderModel->getOrder($virtuemart_order_id);


        $answerKey = vRequest::getString ('Answer', 0);

        $requestKey = $_COOKIE['RequestKey'];

        $operationId = $order_number;


        require_once ('cs/TPConnector.php');
        $tpconnector = new TPConnector();
        $connector_data = $tpconnector->createTPConnector($method);

        $connector = $connector_data['connector'];
        $security_code = $connector_data['security'];
        $merchant = $connector_data['merchant'];

        $optionsGAA = array (

            'Security'   => $security_code,
            'Merchant'   => $merchant,
            'RequestKey' => $requestKey,
            'AnswerKey'  => $answerKey // *Importante
        );




        $rta2 = $connector->getAuthorizeAnswer($optionsGAA);
        $this->logInfo("Tp - GAA: ".json_encode($rta2), "message");
        if ($rta2['StatusCode']== -1){



            if ($rta2['Payload']['Answer']['PAYMENTMETHODNAME']== 'PAGOFACIL' || $rta2['Payload']['Answer']['PAYMENTMETHODNAME']== 'RAPIPAGO' ){

                $new_status = $method->tp_order_status_offline;

                $msj = '<a href="">Imprimir Cup&oacute;n</a>';

            }

            else{



                $new_status = $method->tp_order_status_aproved;

                $msj = $rta2['StatusMessage'];

            }





            if ($virtuemart_order_id) {



                if (!class_exists('VirtueMartModelOrders'))

                    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );

                $modelOrder = new VirtueMartModelOrders();
                $order['order_status'] = $new_status;
                $order['virtuemart_order_id'] = $virtuemart_order_id;
                $order['customer_notified'] = 1;
                $order['comments'] = JTExt::sprintf($msj , $order_number);

                $modelOrder->updateStatusForOneOrder($virtuemart_order_id, $order, false);
                echo "Order id: #".$order_number;
                $cart = VirtueMartCart::getCart ();
                $cart->emptyCart();
            }

        }

        else{

            echo "Operación Rechazada";

        }



        vmdebug ('TODOPAGO plgVmOnPaymentResponseReceived', $mb_data);
        $payment_name = $this->renderPluginName ($method);

        $html = '';
        $link=	JRoute::_("index.php?option=com_virtuemart&view=orders&layout=details&order_number=".$order['details']['BT']->order_number."&order_pass=".$order['details']['BT']->order_pass, false) ;
        $html .='<br />
		<a class="vm-button-correct" href="'.$link.'">'.vmText::_('COM_VIRTUEMART_ORDER_VIEW_ORDER').'</a>';

        return TRUE;

    }



    function plgVmOnUserPaymentCancel () {

        if (!class_exists ('VirtueMartModelOrders')) {
            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }

        $tp_cart = json_decode($_SESSION['__vm']['vmcart']);
      
        $vm_order = VirtueMartModelOrders::getOrderIdByOrderNumber($tp_cart->order_number);
        $orderModel = VmModel::getModel('orders');
        $order = $orderModel->getOrder($vm_order);

        if($this->_vmpCtable->virtuemart_paymentmethod_id!=$order['details']['BT']->virtuemart_paymentmethod_id){
            return NULL;
        }

        echo '<script>alert("Pago Cancelado: Por favor intente nuevamente")</script>';


        $modelOrder = new VirtueMartModelOrders();
        $order['order_status'] = 'X';
        $order['virtuemart_order_id'] = $vm_order;
        $order['customer_notified'] = 1;
        $order['comments'] = JTExt::sprintf("Pago Rechazado" , $vm_order);

        $modelOrder->updateStatusForOneOrder($vm_order, $order, false);

        return true;    }


    function plgVmOnPaymentNotification () {

        if (!class_exists ('VirtueMartModelOrders')) {

            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');

        }



        $mb_data = vRequest::getPost();


        if (!isset($mb_data['transaction_id'])) {


            return;


        }

        $order_number = $mb_data['transaction_id'];

        if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber ($mb_data['transaction_id']))) {

            return;
        }


        if (!($payment = $this->getDataByOrderId ($virtuemart_order_id))) {


            return;
        }


        $method = $this->getVmPluginMethod ($payment->virtuemart_paymentmethod_id);


        if (!$this->selectedThisElement ($method->payment_element)) {

            return FALSE;
        }



        if (!$payment) {


            return NULL;
        }



        $this->_storeInternalData ($method, $mb_data, $virtuemart_order_id);


        $modelOrder = VmModel::getModel ('orders');

        $vmorder = $modelOrder->getOrder ($virtuemart_order_id);

        $order = array();

        $error_msg = $this->_processStatus ($mb_data, $vmorder, $method);

        if ($error_msg) {


            $order['customer_notified'] = 0;
            $order['order_status'] = $method->status_canceled;
            $order['comments'] = 'process IPN ' . $error_msg;
            $modelOrder->updateStatusForOneOrder ($virtuemart_order_id, $order, TRUE);
            $this->logInfo ('process IPN ' . $error_msg, 'ERROR');
        } else {

            $this->logInfo ('process IPN OK', 'message');
        }


        if (empty($mb_data['payment_status']) ||

            ($mb_data['payment_status'] != 'Completed' &&

             $mb_data['payment_status'] != 'Pending')

           ) { // can't get status or payment failed


            //return false;
        }



        $order['customer_notified'] = 1;


        if (strcmp ($mb_data['payment_status'], 'Completed') == 0) {

            $order['order_status'] = $method->status_success;

            $order['comments'] = vmText::sprintf ('VMPAYMENT_TODOPAGO_PAYMENT_STATUS_CONFIRMED', $order_number);


        } elseif (strcmp ($mb_data['payment_status'], 'Pending') == 0) {

            $order['comments'] = vmText::sprintf ('VMPAYMENT_TODOPAGO_PAYMENT_STATUS_PENDING', $order_number);

            $order['order_status'] = $method->status_pending;

        }

        else {

            $order['order_status'] = $method->status_canceled;

        }


        $this->logInfo ('plgVmOnPaymentNotification return new_status:' . $order['order_status'], 'message');

        $modelOrder->updateStatusForOneOrder ($virtuemart_order_id, $order, TRUE);

        $this->emptyCart ($payment->user_session, $mb_data['transaction_id']);
    }


    function plgVmOnShowOrderBEPayment ($virtuemart_order_id, $payment_method_id) {

        $order = new VirtueMartModelOrders;

        $my_order = $order->getOrder($virtuemart_order_id);

        $html = '';

        $htmlStatus = '';

        $gettpstatus = vRequest::getString ('gettpstatus', 0);

        if ($gettpstatus == 1){

            $method = $this->getVmPluginMethod($payment_method_id);
            require_once ('cs/TPConnector.php');
            $tpconnector = new TPConnector();
            $connector_data = $tpconnector->createTPConnector($method);
            $connector = $connector_data['connector'];
            $security_code = $connector_data['security'];
            $merchant = $connector_data['merchant'];

            $optionsGS = array('MERCHANT'=> $merchant, 'OPERATIONID'=> $my_order['details']['BT']->order_number);

            $status = $connector->getStatus($optionsGS);

            if (isset($status['Operations'])){

                if (is_array($status['Operations'])){

                    foreach($status['Operations'] as $index => $value){


                        $htmlStatus .= '<tr>';

                        $htmlStatus .= '<td>';

                        $htmlStatus .= ''.$index.': ';

                        $htmlStatus .= '</td>';

                        $htmlStatus .= '<td>';

                        $htmlStatus .= ''.$value.'';

                        $htmlStatus .= '</td>';

                        $htmlStatus .= '</tr>';

                    }

                }

            }

        }



        $html = '<table class="adminlist table ">' . "\n";

        $html .= '<thead>' . "\n";

        $html .= '<tr>' . "\n";

        $html .= '<th colspan="2">' . "\n";

        $html .= 'TodoPago Status' . "\n";

        $html .= '</th>' . "\n";

        $html .= '</tr>' . "\n";

        $html .= '</thead>' . "\n";

        $html .= '<tr>' . "\n";

        $html .= '<td colspan="2">' . "\n";

        $html .= '<input onclick="getTPStatus()" style="cursor:pointer;width: 300px; margin-left: auto; display: block; margin-right: auto; height: 50px;" id="updateTPStatus" type="button" value="Get TodoPago Status">' . "\n";

        $html .= '</td>' . "\n";

        $html .= '</tr>' . "\n";

        $html .= $htmlStatus;

        $html .= '</table>' . "\n";


        $js = '<script type="text/javascript">

	function getTPStatus(){

		window.location.href =  window.location.href+"&gettpstatus=1";

	}

	</script>';

        $html.=$js;

        return $html;

    }


    protected function checkConditions ($cart, $method, $cart_prices) {


        $this->convert_condition_amount($method);

        $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

        $amount = $this->getCartAmount($cart_prices);



        $amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount


                        OR

                        ($method->min_amount <= $amount AND ($method->max_amount == 0)));


        $countries = array();

        if (!empty($method->countries)) {

            if (!is_array ($method->countries)) {

                $countries[0] = $method->countries;

            } else {

                $countries = $method->countries;
            }

        }


        if (!is_array ($address)) {
            $address = array();
            $address['virtuemart_country_id'] = 0;
        }


        if (!isset($address['virtuemart_country_id'])) {
            $address['virtuemart_country_id'] = 0;
        }



        if (in_array ($address['virtuemart_country_id'], $countries) || count ($countries) == 0) {

            if ($amount_cond) {

                return TRUE;

            }

        }

        return FALSE;
    }




    function plgVmOnStoreInstallPaymentPluginTable ($jplugin_id) {

        return $this->onStoreInstallPluginTable ($jplugin_id);
    }



    /**
	 * This event is fired after the payment method has been selected. It can be used to store
	 * additional payment info in the cart.
	 *
	 * @author Max Milbers
	 * @author ValÃ©rie isaksen
	 *
	 * @param VirtueMartCart $cart: the actual cart
	 * @return null if the payment was not selected, true if the data is valid, error message if the data is not valid
	 */



    public function plgVmOnSelectCheckPayment (VirtueMartCart $cart,  &$msg) {

        $this->tp_states = vRequest::getVar('tp_states');
        return $this->OnSelectCheck ($cart);
    }


    /**
	 * plgVmDisplayListFEPayment
	 * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for exampel
	 *
	 * @param object  $cart Cart object
	 */


    public function plgVmDisplayListFEPayment (VirtueMartCart $cart, $selected = 0, &$htmlIn) {


        if ($this->getPluginMethods($cart->vendorId) === 0) {

            if (empty($this->_name)) {

                $app = JFactory::getApplication();

                $app->enqueueMessage(vmText::_('COM_VIRTUEMART_CART_NO_' . strtoupper($this->_psType)));

                return FALSE;

            } else {

                return FALSE;

            }

        }

        $html = array();

        $method_name = $this->_psType . '_name';

        VmConfig::loadJLang('com_virtuemart', true);

        vmJsApi::jCreditCard();

        $htmla = '';

        $html = array();

       foreach ($this->methods as $this->_currentMethod){

            $methodSalesPrice = $this->setCartPrices($cart, $cart->cartPrices, $this->_currentMethod);
            $this->_currentMethod->$method_name = $this->renderPluginName($this->_currentMethod);
            $html = $this->getPluginHtml($this->_currentMethod, $selected, $methodSalesPrice);

            $states = Helper::getTPStates();//$this->getTPStates();
            $states_html = '<select name="tp_states" id="tp_states">';
            foreach($states as $city => $code){

                $states_html.= '<option value="'.$code.'">'.$city.'</option>';

            }

            $states_html.= '</select>';


            $html .= '<br /><br />
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr valign="top" style="border:none">
			<td nowrapalign="left" >
			<img src="'.JURI::root().'plugins/vmpayment/todopago/logo.jpg" />
			<br /><br />
			<label for="cc_type">Elige la provincia</label>
			'.$states_html.'</td>
			</tr>
			</table><br />';

            $htmla[] = $html;
        }

        $htmlIn[] = $htmla;

        return TRUE;
    }






    public function plgVmonSelectedCalculatePricePayment (VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {


        return $this->onSelectedCalculatePrice ($cart, $cart_prices, $cart_prices_name);

    }


    /**
	 * plgVmOnCheckAutomaticSelectedPayment
	 * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
	 * The plugin must check first if it is the correct type
	 *
	 * @author Valerie Isaksen
	 * @param VirtueMartCart cart: the cart object
	 * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
	 *
	 */

    function plgVmOnCheckAutomaticSelectedPayment (VirtueMartCart $cart, array $cart_prices = array(), &$paymentCounter) {


        return 0;//$this->onCheckAutomaticSelected ($cart, $cart_prices, $paymentCounter);

    }

    /**
	 * This method is fired when showing the order details in the frontend.
	 * It displays the method-specific data.
	 *
	 * @param integer $order_id The order ID
	 * @return mixed Null for methods that aren't active, text (HTML) otherwise
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 */

    public function plgVmOnShowOrderFEPayment ($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {
        /*
        $order = new VirtueMartModelOrders;
        $my_order = $order->getOrder($virtuemart_order_id);

        $method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id);
        require_once ('cs/TPConnector.php');
        $tpconnector = new TPConnector();
        $connector_data = $tpconnector->createTPConnector($method);
        $connector = $connector_data['connector'];
        $merchant = $connector_data['merchant'];


        $optionsGS = array('MERCHANT'=> $merchant, 'OPERATIONID'=> $my_order['details']['BT']->order_number);
        $status = $connector->getStatus($optionsGS);


        $user =& JFactory::getUser();
        $name =  $user->name;

        if (!isset($status['Operations']['CARDNUMBER'])){

            //  $status['Operations']['BARCODE'] = 1234567890;

            if (isset($status['Operations']['BARCODE'])){

                $barcode = $status['Operations']['BARCODE'];
                // $barcode = '1234567890';

                if($barcode != ""){

                    $amount = 0;
                    $operationid = 0;

                    if (isset($status['Operations']['AMOUNT'])){
                        $amount =    $status['Operations']['AMOUNT'];
                    }
                    if (isset($status['Operations']['OPERATIONID'])){
                        $operationid =    $status['Operations']['OPERATIONID'];
                    }

                    echo $js = Helper::addTPPrintFunction($barcode, 'INTERLEAVED_2_OF_5',$amount, $operationid, $name);
                }
            }
        }

        $this->onShowOrderFE ($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);*/
    }


    /**
	 * This method is fired when showing when priting an Order
	 * It displays the the payment method-specific data.
	 *
	 */



    function plgVmonShowOrderPrintPayment ($order_number, $method_id) {

        return $this->onShowOrderPrint ($order_number, $method_id);
    }



    function plgVmDeclarePluginParamsPaymentVM3( &$data) {

        return $this->declarePluginParams('payment', $data);

    }


    function plgVmSetOnTablePluginParamsPayment ($name, $id, &$table) {

        return $this->setOnTablePluginParams ($name, $id, $table);

    }

    private function _sanitize_string($string){

        $string = htmlspecialchars_decode($string);

        $re = "/\\[(.*?)\\]|<(.*?)\\>/i";
        $subst = "";

        $string = preg_replace($re, $subst, $string);

        $string = preg_replace('/[\x00-\x1f]/','',$string);

        $replace = array("!","'","\'","\"","  ","$","#","\\","\n","\r",
                         '\n','\r','\t',"\t","\n\r",'\n\r','&nbsp;','&ntilde;',".,",",.","+", "%");
        $string = str_replace($replace, '', $string);

        $cods = array('\u00c1','\u00e1','\u00c9','\u00e9','\u00cd','\u00ed','\u00d3','\u00f3','\u00da','\u00fa','\u00dc','\u00fc','\u00d1','\u00f1');
        $susts = array('Á','á','É','é','Í','í','Ó','ó','Ú','ú','Ü','ü','Ṅ','ñ');
        $string = str_replace($cods, $susts, $string);

        $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
        $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
        $string = str_replace($no_permitidas, $permitidas ,$string);

        return $string;
    }
}
