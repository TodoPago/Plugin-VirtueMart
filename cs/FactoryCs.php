<?php
class FactoryCs {

	const RETAIL = "Retail";
	const SERVICE = "Service";
	const DIGITAL_GOODS = "Digital Goods";
	const TICKETING = "Ticketing";

	public static function get_cybersource_extractor($vertical, $order, $customer){
		$instance;
		switch ($vertical) {
			case FactoryCS::RETAIL:
				$instance = new Retail($order, $customer);
			break;

			case FactoryCs::SERVICE:
				$instance = new Service($order, $customer);
			break;

			case FactoryCs::DIGITAL_GOODS:
				$instance = new DigitalsGoods($order, $customer);
			break;

			case FactoryCs::TICKETING:
				$instance = new Ticketing($order, $customer);
			break;

			default:
				$instance = new Retail($order, $customer);
			break;
		}
		return $instance;
	}
}