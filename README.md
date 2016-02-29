<a name="inicio"></a>
VIRTUEMART
==========

Plug in para la integración con gateway de pago <strong>Todo Pago</strong>
- [Consideraciones Generales](#consideracionesgenerales)
- [Instalación](#instalacion)
- [Configuración](#configuracion)
- [Configuración plug in](#confplugin)

<a name="consideracionesgenerales"></a>
## Consideraciones Generales
El plug in de pagos de <strong>Todo Pago</strong>, provee a las tiendas VIRTUEMART de un nuevo m&eacute;todo de pago, integrando la tienda al gateway de pago.
La versión de este plug in esta testeada en PHP 5.3-5.4-5.6, VIRTUEMART 3.0+ Y JOOMLA 2.5+

<a name="instalacion"></a>
## Instalación
Observación: Descomentar: <em>extension=php_soap.dll</em> y <em>extension=php_openssl.dll</em> del php.ini, ya que para la conexión al gateway se utiliza la clase SoapClient del API de PHP. 

1.  Extensions->plugin TodoPago -> Install
2.	Subir el archivo .zip
3.	Extensions->Extension Manager buscar el plugin TodoPago y habilitarlo
<sub><em>Extension Manager</em></sub>
![imagen Extension Manager](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/extension-manager.png)



[<sub>Volver a inicio</sub>](#inicio)

<a name="configuracion"></a>
##Configuración

[configuración plug in](#confplugin).
<a name="confplugin"></a>
####Configuración plug in
Para llegar al menu de configuración ir a: <em>Virtuemart -> Payment Methods -> New</em>, completar el formulario y elegir TodoPago entre los medios de pago y grabar<br />
Al grabar dirigirse a la tab Configuration y completar los datos de conexión y usuario de TodoPago.
<br />
<sub><em>Payment Methods</em></sub>
![imagen Payment Methods](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/payment-methods-1.png)

![imagen Payment Methods](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/payment-methods-2.png)


[<sub>Volver a inicio</sub>](#inicio)

####Consideraciones Generales (para todos los verticales, por defecto RETAIL)
El plug in, toma valores est&aacute;ndar del framework para validar los datos del comprador.
Para acceder a los datos de TodoPago se utilizar el objeto $method que se puede crear de la forma: $method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id).
<br />
Para acceder a los datos del vendedor, productos y carrito se usan los objetos $cart y $order que llegan como parámetro en los métodos en los que se necesitan. 
<br />
Este es un ejemplo de la mayoría de los campos que se necesitan para comenzar la operación <br />
<br />
'CSBTCITY'=>$cart->BT['city'], //Ciudad de facturación, MANDATORIO.		
<br />
'CSBTCUSTOMERID'=>$cart->user->customer_number, 
<br />
'CSBTIPADDRESS'=>$this->getTodoPagoClientIp(),	
<br />
'CSBTEMAIL'=>$cart->BT['email'], 	
<br />
'CSBTFIRSTNAME'=>$cart->BT['first_name'] ,		
<br />
'CSBTLASTNAME'=>$cart->BT['last_name'], 		
<br />
'CSBTPHONENUMBER'=>$cart->BT['phone_1'],		
<br />
'CSBTPOSTALCODE'=>$cart->BT['zip'], 
<br />
'CSBTSTATE'=>$this->tp_states, 
<br />
'CSBTSTREET1'=>$cart->BT['address_1'], 
<br />
'CSPTGRANDTOTALAMOUNT'=>$cart->cartPrices['billTotal'],                                 
<br />
'AMOUNT' => $cart->cartPrices['billTotal']	

<br />
Los únicos modelos que tenemos que incluir para obtener los datos restantes son 'Customfields', 'currency' e invocar a los métodos staticos de la clase ShopFunctions.<br />

$customFieldsModel = VmModel::getModel ('Customfields');<br />
$customFieldsModel->getCustomEmbeddedProductCustomFields($prod->virtuemart_product_id);    <br />   <br />
$currency_model = VmModel::getModel('currency');<br />
$currency = $currency_model->getCurrency($order['details']['BT']->user_currency_id);<br />;<br />
$countryIso = ShopFunctions::getCountryByID($order['details']['BT']->virtuemart_country_id,'country_2_code');<br />
$countryName = ShopFunctions::getCountryByID($order['details']['BT']->virtuemart_country_id);<br />
####Devoluciones
Se pueden realizar devoluciones mediante la "Consola de Todopago", simplemente debe ingresar el monto a devolver al comprador con el siguiente formato: 1.45

En caso que la devolución se realice de manera afirmativa se vera un mensaje como el detallado en la imagen siguiente, en caso contrario se podra ver el motivo del fallo.
![imagen Payment Methods](https://github.com/TodoPago/imagenes/blob/master/virtuemart/Seleccion_004.png)

####Muy Importante
<strong>Provincias:</strong> Al ser un campo MANDATORIO para enviar y propio del plugin este campo se completa por parte del usuario al momento del check out.
<br />
[<sub>Volver a inicio</sub>](#inicio)
