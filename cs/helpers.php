<?php 

class Helper {

	public static function getTPStates(){

			$states = array('CABA' => 'C',
				'Buenos Aires'  => 'B',
				'Catamarca'  => 'K',
				'Chaco'  => 'H' ,
				'Chubut'  => 'U',
				'C&oacute;rdoba'  => 'X',
				'Corrientes'  => 'W',
				'Entre R&iacute;os'  => 'R',
				'Formosa'  => 'P',
				'Jujuy'  => 'Y',
				'La Pampa'  => 'L',
				'La Rioja' =>  'F',
				'Mendoza' => 'M',
				'Misiones'  => 'N',
				'Neuqu&eacute;n'  => 'Q',
				'R&iacute;o Negro'  => 'R',
				'Salta'  => 'A',
				'San Juan'  => 'J',
				'San Luis'  => 'D',
				'Santa Cruz'  => 'Z',
				'Santa F&eacute;' =>  	'S',
				'Santiago del Estero'  => 'G',
				'Tierra del Fuego'  => 'V',
				'Tucum&aacute;n'  => 'T');

			return $states;

		}

	public static function getTodoPagoClientIp() {

		$ipaddress = '';

		if ($_SERVER['HTTP_CLIENT_IP'])

			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];

		else if($_SERVER['HTTP_X_FORWARDED_FOR'])

			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];

		else if($_SERVER['HTTP_X_FORWARDED'])

			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];

		else if($_SERVER['HTTP_FORWARDED_FOR'])

			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];

		else if($_SERVER['HTTP_FORWARDED'])

			$ipaddress = $_SERVER['HTTP_FORWARDED'];

		else if($_SERVER['REMOTE_ADDR'])

			$ipaddress = $_SERVER['REMOTE_ADDR'];

		else

			$ipaddress = 'UNKNOWN';

		return $ipaddress;
	}

	public static function addTPPrintFunction($text, $bartype, $amount, $orden, $name){

		$logo = JURI::root()."plugins/vmpayment/todopago/logo.jpg";
		$button = "<a  style='cursor:pointer' class='tp_print_button' onclick='tp_print_button()'>
		<b>imprimir codigo TodoPago</b><br/><br/>
		<img src='".$logo."' />
	</a>";

	$params = base64_encode('name='.$name.'&orden='.$orden.'&amount='.$amount.'&logo='.$logo.'&filetype=PNG&dpi=72&scale=2&rotation=0&font_family=Arial.ttf&font_size=8&text='.$text.'&thickness=30&checksum=&code='.$bartype.'');

	$js = '<script type="text/javascript">
	function tp_print_button(){

		window.open("'.JURI::root().'plugins/vmpayment/todopago/cupon/print_cupon.php?params='.$params.'");

	}
</script>';

return $button.$js;
}

	public static function mergeCommonExtraFields($operation, $extra){

		$result = array_merge($operation, $extra);
		return $result;

	}

	public static  function getTableSQLFields(){

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
}