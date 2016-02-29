<?php 

function isTodopagoOrder($virtuemart_order_id){
// Get a db connection.
	$db = JFactory::getDbo();

// Create a new query object.
	$query = $db->getQuery(true);

// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
	$query->select($db->quoteName(array('order_number')));
	$query->from($db->quoteName('#__virtuemart_payment_plg_todopago'));
	$query->where($db->quoteName("virtuemart_order_id")."=".$db->quote($_GET['virtuemart_order_id']));
		// Reset the query using our newly populated query object.
	$db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$payment_element = $db->loadObjectList();
	
	$_return  = TRUE;
	if(empty($payment_element)){
		return FALSE;
	}

	return $_return;

}