<?php

defined('_JEXEC') or die;

require_once('sdk/Sdk.php');

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

require_once ("sdk/Sdk.php");

class JFormFieldVmMediosdepago extends JFormFieldList {

	var $type = 'vmmediosdepago';

	public function getOptions()
	{
		$_my_settings = $this->_get_settings();

		$options = array();

		$pipi = new Sdk ($this->get_http_header(), $this->get_ambiente());

		$payment_method = $pipi->discoverPaymentMethods();
		$payment_method_array = $payment_method['PaymentMethod'];

		foreach ($payment_method_array as $key => $value) {
			$options[] = array("value"=>$value['ID'], "text"=>$value["Name"]); 	
		}

		return array_merge(parent::getOptions(), $options);
		
	}


	private function get_ambiente()
	{
		$esta = $this->_get_settings();
		$esta = $esta['tp_ambiente'];
		return json_decode($esta);
		
	}

	private function get_http_header()
	{
		$esta = $this->_get_settings();
		$esta = $esta['tp_auth_http'];
		return ((array)(json_decode(json_decode($esta))));
		
	}

	private function _get_settings()
	{
				// Get a db connection.
		$db = JFactory::getDbo();

// Create a new query object.
		$query = $db->getQuery(true);

// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
		$query->select($db->quoteName(array('payment_params')));
		$query->from($db->quoteName('#__virtuemart_paymentmethods'));
		$query->where($db->quoteName('payment_element')."=".$db->quote('todopago'));
		// Reset the query using our newly populated query object.
		$db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$payment_element = $db->loadObjectList()[0]->payment_params;
		
		$my_settings_array = array();

		$my_settings = explode("|", $payment_element);
		foreach ($my_settings as $key => $value) {
			$_value = explode("=", $value);
			$my_settings_array[$_value[0]]= $_value[1];
		}
		return $my_settings_array;
	}


}
