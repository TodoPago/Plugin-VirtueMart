<?php

class FactoryTodopago
{

	public static function get_extractor($vertical, $cart, $customFieldsModel){
		$extra_fields="";
		switch ('retail'){//($vertical) {

		case 'retail':
		require('Retail.php');
		$retail_model = new Retail();
		$extra_fields = $retail_model->getFields($cart, $customFieldsModel);
		break;

		case 'ticketing':
		require('Ticketing.php');
		$ticketing_model = new Ticketing();
		$extra_fields = $ticketing_model->getFields($cart, $customFieldsModel);
		break;

		case 'services':
		require('Services.php');
		$services_model = new Services();
		$extra_fields = $services_model->getFields($cart, $customFieldsModel);
		break;

		case 'digital':
		require('Digitalgoods.php');
		$digitalgoods_model = new Digitalgoods();
		$extra_fields = $digitalgoods_model->getFields($cart, $customFieldsModel);
		break;

		default:
		require('Retail.php');
		$retail_model = new Retail();
		$extra_fields = $retail_model->getFields($cart, $customFieldsModel);
		break;
		}
		return $extra_fields;
	}
}