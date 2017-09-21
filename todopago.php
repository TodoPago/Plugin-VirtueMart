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
use TodoPago\Sdk as Sdk;

defined ('_JEXEC') or die('Restricted access');

if (!class_exists ('vmPSPlugin')) {
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}
require_once (dirname(__FILE__) . '/vendor/autoload.php');
//require('sdk/Sdk.php');
require ('cs/helpers.php');

require_once ('cs/TPConnector.php');
require_once ('cs/StatusCodeCS.php');

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
        $this->db = JFactory::getDbo();

        $varsToPush = array(
            'tp_vertical_type'    => array('', 'char'),
            'tp_canal_ingreso'    => array('', 'char'),
            'tp_endpoint_test' => array('', 'int'),
            'tp_wsdl_test'   => array('', 'int'),
            'tp_auth_http'          => array('', 'int'),
            'tp_auth_http_test'          => array('', 'int'),
            'tp_auth_http_prod'          => array('', 'int'),
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
            'tp_formulario'=>array('', 'char'),
            'tp_cuotas_enabled'  => array('', 'int'),
            'tp_cuotas'=>array('', 'char'),
            'tp_form_timeout_enabled'=>array('', 'int'),
            'tp_form_timeout'       => array('', 'int'),
            'tp_emptycart_enabled'       => array('', 'int'),
            'tp_gmaps_enabled'   => array('', 'int'),
        );

        $this->setConfigParameterable ($this->_configTableFieldName, $varsToPush);

        $app = JFactory::getApplication();    
        $view = (isset($_GET['view']))?$_GET['view']:'';
        if($app->isAdmin() && $this->isTodopagoPaymentPage($view) ){
            $this->display_btn_credentials();    
        }

        $this->tpFinancialCost($view);
        $this->getVmPluginCreateTableAddressSQL();
    }

    function tpFinancialCost($view){

        if ($view != 'orders') return false;
           
        $order_number = $_REQUEST['order_number'];
        if(!isset($order_number)) return false;

        try{
            $db = JFactory::getDbo();
            
        }catch (Exception $e)
        {
            $this->setError($e->getMessage());
            return false;
        }
         
        
        $query = $db->getQuery(true);    
//        $query->select($db->quoteName(array('order_number','order_total')));
        $query->select($db->quoteName(array('*')));
        $query->from($db->quoteName('#__virtuemart_orders'));
        $query->where($db->quoteName('order_number') . "='" .$order_number ."'");
        // Reset the query using our newly populated query object.
        $db->setQuery($query);

        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $order_result = $db->loadObjectList();
        $value = $order_result['0']->order_total;
        $virtuemart_order_id = $order_result['0']->virtuemart_order_id;      
        $payment_method_id = $order_result['0']->virtuemart_paymentmethod_id;
    
        $order = new VirtueMartModelOrders;
        $my_order = $order->getOrder($virtuemart_order_id);

        $method = $this->getVmPluginMethod($payment_method_id);

        require_once ('cs/TPConnector.php');

        $tpconnector = new TPConnector();
        $connector_data = $tpconnector->createTPConnector($method);
     
        $connector = $connector_data['connector'];
        $security_code = $connector_data['security'];
        $merchant = $connector_data['merchant'];
        $optionsGS = array('MERCHANT'=> $merchant, 'OPERATIONID'=> $order_number);
        
        $status = $connector->getStatus($optionsGS);

        return $result;
    }


    function isTodopagoPaymentPage($view){
        $model = VmModel::getModel('paymentmethod');
        $payment = $model->getPayment();
        return (strtolower($payment->slug)=='todopago' && $view=='paymentmethod' )?true:false;
    }


    function getConnector($virtuemart_paymentmethod_id=2){
        $method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id);
        $tpconnector = new TPConnector();
        return $tpconnector->createTPConnector($method);
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
            'tp_auth_http_test'          => 'varchar(100)',
            'tp_auth_http_prod'          => 'varchar(100)',
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
            'tp_ambiente'      =>  'varchar(100)',
            'tp_formulario' => 'varchar(100)',
            );
        return $SQLfields;
    }

    public function getVmPluginCreateTableSQL () {
        $this->getVmPluginCreateTableAddressSQL();
        return $this->createTableSQL ('Payment Todopago Table');
    }

    private function getVmPluginCreateTableAddressSQL () {
        $db = JFactory::getDBO();
        $query = "CREATE TABLE IF NOT EXISTS `#__todopagoaddress` (
          `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `address` VARCHAR(255) NULL DEFAULT NULL,
          `city` VARCHAR(255) NULL DEFAULT NULL,
          `postal_code` VARCHAR(255) NULL DEFAULT NULL,
          `country` VARCHAR(255) NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        );";
        $db->setQuery($query);
        $db->query();
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

    $Error_message = ( isset($_GET['Error']))? $_GET['Error']: 'El pago no pudo ser realizado';

        $cancel_url = JURI::root().'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&on=' .

        $order['details']['BT']->order_number .

        '&pm=' .

        $order['details']['BT']->virtuemart_paymentmethod_id .

        '&Itemid=' . vRequest::getInt ('Itemid') .
        '&lang='.vRequest::getCmd('lang','') . '&Error='. $Error_message;


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

        $state_2_code = ShopFunctions::getStateByID($order['details']['BT']->virtuemart_state_id,'state_2_code');
        $stateIso = $state_2_code[0];
        $optionsSAR_operacion = $this->getCommonFields($cart, $customFieldsModel, $stateIso);

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
        $optionsSAR_operacion['CSSTSTATE'] = $stateIso;
        $optionsSAR_operacion['CSSTCOUNTRY'] = $countryIso;
        $optionsSAR_operacion['CSMDD12'] = $method->tp_dead_line;
        $optionsSAR_operacion['CSMDD13'] = $this->_sanitize_string($cart->cartData['shipmentName']);
        if(isset($method->tp_cuotas_enabled) && $method->tp_cuotas_enabled==1 ){
            $optionsSAR_operacion['MAXINSTALLMENTS'] = $method->tp_cuotas;
        }
        if(isset($method->tp_form_timeout_enabled) && $method->tp_form_timeout_enabled==1 ){
            $optionsSAR_operacion['TIMEOUT'] = $method->tp_form_timeout;
        }

        $this->logInfo("TP - SARcomercio - ".json_encode($optionsSAR_comercio), "message");

        $this->logInfo("TP - SARoperacion - ".json_encode($optionsSAR_operacion), "message");

/*        // si esta habilitado para la direcciones de gmaps setear cliente google
        $address_result = $this->address_loaded($optionsSAR_operacion);
        $gClient = null;

        if($address_result['address_loaded']){ 
            $optionsSAR_operacion = $address_result['payDataOperacion'];
            $order = $this->update_addresses($order, $address_result['payDataOperacion']);

        }elseif ($method->tp_gmaps_enabled == 1){
            $gClient = new \TodoPago\Client\Google();

            if($gClient != null) {
                $connector->setGoogleClient($gClient);
            }
           
        }
*/        
        $this->logInfo("llamada al SAR", "message");
        
        try{
            $rta = $connector->sendAuthorizeRequest($optionsSAR_comercio, $optionsSAR_operacion);
        }catch (Exception $e){
            $this->logInfo(json_encode($e), "message");
        }

        //guardo direccion 
  /*      if($gClient != null) {
            $this->tp_save_address($connector->getGoogleClient()->getFinalAddress());
            // modify addresses
            $order = $this->update_addresses($order, $connector->getGoogleClient()->getFinalAddress());
        }
*/
        $this->logInfo("TP - SAR rta - ".json_encode($rta), "message");
        
  
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
        $dbValues["tp_security_code_prod"] = $rta["RequestKey"];

        $this->storePSPluginInternalData ($dbValues);


        $cart->_confirmDone = TRUE;

        $cart->_dataValidated = TRUE;

        $cart->setCartIntoSession ();

        if ($rta['StatusCode']!= -1){
            echo "<script>
                    console.log('".$statusMessage."');
                    alert('Su pago no puede ser procesado. Intente nuevamente más tarde. ".$statusMessage."');
                  </script>";
            $this->logInfo("TP - Redirect to: ".$rta['URL_Request'], "message");
         
            echo "<script>window.location.href = '".JURI::root()."index.php/cart/'</script>";

        }else{
            switch ($method->tp_formulario) {
                case '1':
                    $this->_get_formulario($rta, $optionsSAR_operacion, $optionsSAR_comercio, $method->tp_ambiente);
                
                break;
                
                default:
                    $this->logInfo("TP - Redirect to: ".$rta['URL_Request'], "message");
                    header('Location: '.$rta['URL_Request']);
                break;
            }
            
            
        }
    }

    private function address_loaded($payDataOperacion){
       
        $CSBT_address = $this->get_loaded_address($payDataOperacion, 'CSBT');
        $CSST_address = $this->get_loaded_address($payDataOperacion, 'CSST');
       
        if (($CSBT_address != null) && ( $CSST_address != null ) ){
            $payDataOperacion['CSBTSTREET1']= $CSBT_address->address;
            $payDataOperacion['CSBTPOSTALCODE']= $CSBT_address->postal_code;
            $payDataOperacion['CSBTCITY']= $CSBT_address->city;
            $payDataOperacion['CSBTCOUNTRY']= $CSBT_address->country;

            $payDataOperacion['CSSTSTREET1']= $CSST_address->address;
            $payDataOperacion['CSSTPOSTALCODE']= $CSST_address->postal_code;
            $payDataOperacion['CSSTCITY']= $CSST_address->city;
            $payDataOperacion['CSSTCOUNTRY']= $CSST_address->country;

            $address_loaded = true;
        }else{ 
            $address_loaded = false;
        }

        $address_result = array('payDataOperacion' => $payDataOperacion,                            
                                'address_loaded' => $address_loaded
                                );

        return $address_result; 
    }

    /**
    *   returns stdClass if exist address, else returns null
    */
    private function get_loaded_address($payDataOperacion, $type){

        $street  = explode(' ', $payDataOperacion["{$type}STREET1"]);

        $where = '';  
        foreach ($street as $val) { 
            $where .= " address like '%{$val}%' and ";
        }

        $query = "SELECT * FROM `#__todopagoaddress` where {$where} postal_code like '%{$payDataOperacion["{$type}POSTALCODE"]}%' and country='{$payDataOperacion["{$type}COUNTRY"]}' limit 1" ;

        $this->db->setQuery($query);
        $res = $this->db->loadObjectList();

        return $res[0];
    }



    private function tp_save_address($payDataOperacion){

        // Get a db connection.
        $query = "INSERT INTO `#__todopagoaddress` (`address`, `city`, `postal_code`, `country`) 
                  VALUES ('{$payDataOperacion['billing']['CSBTSTREET1']}', '{$payDataOperacion['billing']['CSBTCITY']}', '{$payDataOperacion['billing']['CSBTPOSTALCODE']}', '{$payDataOperacion['billing']['CSBTCOUNTRY']}');"; 

        $this->db->setQuery($query);
        $this->db->execute(); 

        if ($this->address_diff($payDataOperacion)){

            $query = "INSERT INTO `#__todopagoaddress` (`address`, `city`, `postal_code`, `country`) 
                  VALUES ('{$payDataOperacion['shipping']['CSSTSTREET1']}', '{$payDataOperacion['shipping']['CSSTCITY']}', '{$payDataOperacion['shipping']['CSSTPOSTALCODE']}', '{$payDataOperacion['shipping']['CSSTCOUNTRY']}');"; 
            $this->db->setQuery($query);
            $this->db->execute();

        } 
        
    }

    private function address_diff($payDataOperacion){
        $result = false; 

        if($payDataOperacion['billing']['CSBTCOUNTRY'] != $payDataOperacion['shipping']['CSSTCOUNTRY']) $result = true; 
        if($payDataOperacion['billing']['CSBTPOSTALCODE'] != $payDataOperacion['shipping']['CSSTPOSTALCODE']) $result = true; 
        if($payDataOperacion['billing']['CSBTCITY'] != $payDataOperacion['shipping']['CSSTCITY']) $result = true; 
        if($payDataOperacion['billing']['CSBTSTREET1'] != $payDataOperacion['shipping']['CSSTSTREET1']) $result = true; 

        return $result;
    }

    private function update_addresses($order,  $payDataOperacion){
    
        $query = "  UPDATE `#__virtuemart_order_userinfos` 
                    SET `address_1` = '{$payDataOperacion['billing']['CSBTSTREET1']}' , 
                      `city`='{$payDataOperacion['billing']['CSBTCITY']}' , 
                      `zip`='{$payDataOperacion['billing']['CSBTPOSTALCODE']}' 
                    WHERE `virtuemart_order_userinfo_id` = {$order['details']['BT']->virtuemart_order_userinfo_id}; " ;

        $this->db->setQuery($query);
        $this->db->execute();
       

        $query = " UPDATE `#__virtuemart_order_userinfos` 
                    SET `address_1` = '{$payDataOperacion['shipping']['CSSTSTREET1']}' , 
                      `city`='{$payDataOperacion['shipping']['CSSTCITY']}' , 
                      `zip`='{$payDataOperacion['shipping']['CSSTPOSTALCODE']}' 
                    WHERE `virtuemart_order_userinfo_id` = {$order['details']['ST']->virtuemart_order_userinfo_id};" ;
 
        $this->db->setQuery($query);
        $this->db->execute();
  
        return false;  

    }




    function _get_formulario($rta, $data_operation, $data_comercial, $ambiente)
    {
        include("views/formularioTP/formulario-TP.php");
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
            
            if (empty($product_description)){
                $product_description =  $this->_sanitize_string(trim(urlencode(htmlentities(strip_tags($prod->product_s_desc)))));
            }

            $CSITPRODUCTDESCRIPTION[] = substr($product_description, 0, 10);
            $CSITPRODUCTNAME[] =  trim(urlencode(htmlentities(strip_tags($prod->product_name))));
            $CSITPRODUCTSKU[] = $prod->product_sku;
            $CSITTOTALAMOUNT[] = number_format(($prod->prices['salesPrice'] * $prod->amount),2,".", "");
            $CSITQUANTITY[] = intval($prod->amount);
            $CSITUNITPRICE[] = number_format($prod->prices['salesPrice'],2,".","");

//            $customfields = $customFieldsModel->getCustomEmbeddedProductCustomFields($prod->virtuemart_product_id);
        }

        /** COMMON FIELDS **/

        $fields = array(

            'CSBTCITY'=>$cart->BT['city'], //Ciudad de facturación, MANDATORIO.
            'CSBTCUSTOMERID'=>$cart->user->customer_number, //Identificador del usuario al que se le emite la factura. MANDATORIO. No puede contener un correo electrónico.
            'CSBTIPADDRESS'=>($this->get_the_user_ip() == '::1') ? '127.0.0.1' : $this->get_the_user_ip(), //Helper::getTodoPagoClientIp(), //IP de la PC del comprador. MANDATORIO.
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

        $this->logInfo("Tp - GAA REQUEST : ".json_encode($optionsGAA), "message");

        $rta2 = $connector->getAuthorizeAnswer($optionsGAA);
    
        $this->logInfo("Tp - GAA: ".json_encode($rta2), "message");

            if ($method->tp_emptycart_enabled){
                $cart = VirtueMartCart::getCart();
                $cart->emptyCart();
            }


        if ($rta2['StatusCode']== -1){
            if ($rta2['Payload']['Answer']['PAYMENTMETHODNAME']== 'PAGOFACIL' || $rta2['Payload']['Answer']['PAYMENTMETHODNAME']== 'RAPIPAGO' ){
                $new_status = $method->tp_order_status_offline;
                $msj = '<a href="">Imprimir Cup&oacute;n</a>';
            } else {
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

        $total_cost = $rta2['Payload']['Request']['AMOUNTBUYER'];
        $value = $rta2['Payload']['Request']['AMOUNT'];
        $tax = $total_cost - $value;
        
        $db = JFactory::getDbo();
        $query2 = $db->getQuery(true);

        // Fields to update.
        $fields = array(
            $db->quoteName('order_total') . ' = ' . $total_cost,
            $db->quoteName('order_payment') . ' = ' . $tax
        );
         
        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('order_number') . "='" .$order_number ."'"
        );
         
        $query2->update($db->quoteName('#__virtuemart_orders'))->set($fields)->where($conditions);
         
        $db->setQuery($query2);
        
        if (!$db->query()) {
            var_dump($db->getErrorMsg());
            //throw new Exception($db->getErrorMsg());
        }

        $result = $db->execute();

            }

        }

        else{

           echo '<script>alert("' . $rta2["StatusMessage"] . '")</script>';
       return null;
        }



        vmdebug ('TODOPAGO plgVmOnPaymentResponseReceived', $mb_data);
        $payment_name = $this->renderPluginName ($method);

        $html = '';
        $link=  JRoute::_("index.php?option=com_virtuemart&view=orders&layout=details&order_number=".$order['details']['BT']->order_number."&order_pass=".$order['details']['BT']->order_pass, false) ;
        $html .='<br />
        <a class="vm-button-correct" href="'.$link.'">'.vmText::_('COM_VIRTUEMART_ORDER_VIEW_ORDER').'</a>';

        return TRUE;

    }


    function tp_GAA($requestKey=null,$answerKey=null, $virtuemart_paymentmethod_id=2){

        $connector_data = $this->getConnector($virtuemart_paymentmethod_id);   
        $connector = $connector_data['connector'];
        $security_code = $connector_data['security'];
        $merchant = $connector_data['merchant'];

        $optionsGAA = array (
            'Security'   => $security_code,
            'Merchant'   => $merchant,
            'RequestKey' => $requestKey,
            'AnswerKey'  => $answerKey // *Importante
        );

        $this->logInfo("Tp - GAA REQUEST : ".json_encode($optionsGAA), "message");

        $rta = $connector->getAuthorizeAnswer($optionsGAA);

        $this->logInfo("Tp - GAA: ".json_encode($rta), "message");

        return $rta;
    }




    function plgVmOnUserPaymentCancel () {
        $this->logInfo(__FUNCTION__);

        if (!class_exists ('VirtueMartModelOrders')) {
            $this->logInfo("la clase no existe");

            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }

        $answerKey = (isset($_GET['Answer']))?$_GET['Answer']:vRequest::getString ('Answer', 0);
        $requestKey = $_COOKIE['RequestKey'];
        $virtuemart_paymentmethod_id = (isset($_GET['pm']))?$_GET['pm']:2;

        if ($answerKey) {
            //si llego aca luego del first step quiere decir que el pago no fue exitoso, debo loguear el GAA
            $rta_GAA = $this->tp_GAA($requestKey,$answerKey, $virtuemart_paymentmethod_id);
        $_GET['Error'] = $rta_GAA['StatusMessage'];     
        }

        $this->logInfo(json_encode($_SESSION['__vm']));

        $tp_cart = json_decode($_SESSION['__vm']['vmcart']);

        $vm_order = VirtueMartModelOrders::getOrderIdByOrderNumber($tp_cart->order_number);
        $orderModel = VmModel::getModel('orders');

        $order = $orderModel->getOrder($vm_order);

        if( isset($_GET['Error'])) {
                 
            $modelOrder = new VirtueMartModelOrders();
            $order['order_status'] = 'X';
            $order['virtuemart_order_id'] = $vm_order;
            $order['customer_notified'] = 0;
            $order['comments'] = JTExt::sprintf("Pago Cancelado: " .  $_GET['Error'] , $vm_order);

            $modelOrder->updateStatusForOneOrder($vm_order, $order, false);
        $method = $this->getVmPluginMethod($payment_method_id);
            
        if ($method->tp_emptycart_enabled){
            $cart = VirtueMartCart::getCart();
            $cart->emptyCart();
            }
            //echo '<script>alert("' . $_GET['Error'] . '");</script>';

            $app = JFactory::getApplication();
            $app->redirect(JRoute::_('index.php', false), vmText::_($_GET['Error']));


            return NULL;

        }


        if($this->_vmpCtable->virtuemart_paymentmethod_id!=$order['details']['BT']->virtuemart_paymentmethod_id){
            $modelOrder = new VirtueMartModelOrders();
            $order['order_status'] = 'X';
            $order['virtuemart_order_id'] = $vm_order;
            $order['customer_notified'] = 0;
            $order['comments'] = JTExt::sprintf("Pago Cancelado: " . $rta_GAA["StatusMessage"] , $vm_order);

            $modelOrder->updateStatusForOneOrder($vm_order, $order, false);
            echo '<script>alert("' . $rta_GAA["StatusMessage"] . '")</script>';
            if ($method->tp_emptycart_enabled){
            $cart = VirtueMartCart::getCart();
            $cart->emptyCart();
            } 

            return NULL;
        }

        echo '<script>alert("' . $rta_GAA["StatusMessage"] . '")</script>';


        $modelOrder = new VirtueMartModelOrders();
        $order['order_status'] = 'X';
        $order['virtuemart_order_id'] = $vm_order;
        $order['customer_notified'] = 0;
        $order['comments'] = JTExt::sprintf("Pago Rechazado" , $vm_order);

        $modelOrder->updateStatusForOneOrder($vm_order, $order, false);
            if ($method->tp_emptycart_enabled){
            $cart = VirtueMartCart::getCart();
            $cart->emptyCart();
            } 

        return true;
    }


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

    $method = $this->getVmPluginMethod($payment_method_id);

    require_once ('cs/TPConnector.php');

    $tpconnector = new TPConnector();
    $connector_data = $tpconnector->createTPConnector($method);

    $connector = $connector_data['connector'];
    $security_code = $connector_data['security'];
    $merchant = $connector_data['merchant'];

    $optionsGS = array('MERCHANT'=> $merchant, 'OPERATIONID'=> $my_order['details']['BT']->order_number);
    if($merchant==""){
    
    }else{
    
    
    $status = $connector->getStatus($optionsGS);

    
    include_once("views/get_status_view.php");
    echo "<hr />";
    include_once("views/get_devoluciones_view.php");


        // Get a db connection.
    $db = JFactory::getDbo();

// Create a new query object.
    $query = $db->getQuery(true);

// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
    $query->select($db->quoteName(array('tp_security_code_prod')));
    $query->from($db->quoteName('#__virtuemart_payment_plg_todopago'));
    $query->where($db->quoteName("virtuemart_order_id")."=".$db->quote($_GET['virtuemart_order_id']));
        // Reset the query using our newly populated query object.
    $db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
    $payment_element = $db->loadObjectList();    


    $_requestKey = $payment_element[0]->tp_security_code_prod;


    /////devoluciones
    if($_POST["dev_params"]=="dev_params"){
        if ($method->tp_ambiente == "test"){

            $security =  $method->tp_security_code_test;
            $merchant = $method->tp_id_site_test;
            $rest_end_point = "https://developers.todopago.com.ar/t/1.1/api/Authorize";
        }
        else{

            $security =  $method->tp_security_code_prod;
            $merchant = $method->tp_id_site_prod;
            $rest_end_point = "https://api.todopago.com.ar/t/1.1/api/Authorize";
        }

        $data = array(
            "Security"=>$security,
            "RequestKey"=>$_requestKey,
            "Merchant"=>$merchant,
            "AMOUNT"=> $_POST["ReturnRequestAmount"]
            );

    require_once ('cs/TPConnector.php');
    $method = $this->getVmPluginMethod($payment_method_id);

    $tpconnector = new TPConnector();
    $connector_data = $tpconnector->createTPConnector($method);

    $connector = $connector_data['connector'];
    $security_code = $connector_data['security'];
    $merchant = $connector_data['merchant'];

        $this->logInfo("TP - Devolucion PARAMETROS - ".json_encode($data), "message");

    $rta = $connector->returnRequest($data);

        $this->logInfo("TP - Devolucion RESPUESTA - ".json_encode($rta), "message");

        if($rta["StatusCode"]==2011){
            echo '<h4 style="color:red">'.$rta["StatusMessage"].'</h4>';
            echo '<script>alert("Su devolucion se ha realizado con exito")</script>';
        }else{
            echo '<h4 style="color:red">'."Ha ocurrido un inconveniente:  ". $rta["StatusMessage"].'</h4>';
        }

    }}
    ////devoluciones

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

            


            $html .= '<br /><br />
        
                     <img src="'.JURI::root().'plugins/vmpayment/todopago/logo.jpg" />
                     
                    <br />';


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
     * @author 
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
    }

    function plgVmonShowOrderPrintPayment ($order_number, $method_id) {

        return $this->onShowOrderPrint ($order_number, $method_id);
    }



    function plgVmDeclarePluginParamsPaymentVM3( &$data) {

        return $this->declarePluginParams('payment', $data);

    }


    function plgVmSetOnTablePluginParamsPayment ($name, $id, &$table) {

        return $this->setOnTablePluginParams ($name, $id, $table);

    }


    function display_btn_credentials(){ 
echo        '<div style="margin-left: 202px; ">
            <div><img src="http://www.todopago.com.ar/sites/todopago.com.ar/files/logo.png"></div>
            <button id="btn-credentials-dev" name="btn-credentials" class="btn-config-credentials" >
                Obtener credenciales Developers
            </button>
            <button id="btn-credentials-prod" name="btn-credentials" class="btn-config-credentials" >
                Obtener credenciales Produccion
            </button>
            <div id="credentials-login" class="order-action-popup" style="display:none;">
                <img src="http://www.todopago.com.ar/sites/todopago.com.ar/files/logo.png">
                <p>Ingresa con tus credenciales para Obtener los datos de configuración</p>    
                    <input id="mode" name="mode" type="hidden" value="">
                    <label class="control-label">E-mail</label>
                    <input id="mail" class="form-control" name="mail" type="email" value="" placeholder="E-mail"/>
                    <label class="control-label">Contrase&ntilde;a</label>
                    <input id="pass" class="form-control" name="pass" type="password" value="" placeholder="Contrase&ntilde;a"/>
                    <button id="btn-form-credentials" style="margin:15%;" class="btn-config-credentials" >Acceder</button>
            </div>

        </div>

        <script type="text/javascript">
        
        jQuery( document ).ready(function() {
                var x = document.getElementsByName("payment_name");
                var y = document.getElementsByName("slug");

                x[0].value = "Todo Pago"
                y[0].value = "todopago"

        jQuery(x).prop("readonly", true);
        jQuery(y).prop("readonly", true);
            });

jQuery( document ).ready(function() {

Joomla.submitbutton=function(a){
              if (jQuery("#params_tp_form_timeout").val()) {
            if(jQuery("#params_tp_form_timeout").val() > 6*60*60*1000)
                jQuery("#params_tp_form_timeout").val(6*60*60*1000)
            if(jQuery("#params_tp_form_timeout").val() < 5*60*1000)
                jQuery("#params_tp_form_timeout").val(5*60*1000)
            }

        var options = { path: "/", expires: 2}
        if (a == "apply") {
            var idx = jQuery("#tabs li.current").index();
            jQuery.cookie("vmapply", idx, options);
        } else {
            jQuery.cookie("vmapply", "0", options);
        }
        jQuery( "#media-dialog" ).remove();
        form = document.getElementById("adminForm");
        form.task.value = a;
        form.submit();
        return false;
    };

});
            jQuery("#btn-credentials-dev").click(function(){
                jQuery( "#credentials-login" ).dialog();
                jQuery("#mode").val("test");
            });

            jQuery("#btn-credentials-prod").click(function(){              
                jQuery( "#credentials-login" ).dialog();
                jQuery("#mode").val("prod");
            });


            jQuery("#btn-form-credentials").click(function(){ 
                console.log("obtengo credenciales por ajax.");
                jQuery.post( "../plugins/vmpayment/todopago/credentials_form.php", 
                        { mail: jQuery("#mail").val(), 
                          pass: jQuery("#pass").val(),
                          mode: jQuery("#mode").val()
                        }, function(data){ 
                            console.log(data);
                            var obj = jQuery.parseJSON( data );
                            if (obj.error_message != "0"){
                                console.log(obj.error_message);
                                alert(obj.error_message);
                            }else{ 
                                if (obj.ambiente == "test"){
                                    jQuery("input:text[id=params_tp_auth_http_test]").val(obj.Authorization);
                                    jQuery("input:text[id=params_tp_id_site_test]").val(obj.merchantId);
                                    jQuery("input:text[id=params_tp_security_code_test]").val(obj.apiKey);
                                }else{
                                    jQuery("input:text[id=params_tp_auth_http_prod]").val(obj.Authorization);
                                    jQuery("input:text[id=params_tp_id_site_prod]").val(obj.merchantId);
                                    jQuery("input:text[id=params_tp_security_code_prod]").val(obj.apiKey);
                                }
                            }    
                        }
                );
                jQuery("#credentials-login").dialog("close");
            });
                   
        </script>';

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



}
