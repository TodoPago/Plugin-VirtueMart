<a name="inicio"></a>
VIRTUEMART
==========

Plug in para la integración con gateway de pago <strong>Todo Pago</strong>
- [Consideraciones Generales](#consideracionesgenerales)
- [Instalación](#instalacion)
- [Configuración](#configuracion)
 - [Configuración plug in](#confplugin)
 - [Formulario Hibrido](#formHibrido)
 - [Nuevas columnas y atributos](#tca)
- [Prevencion de Fraude](#cybersource)
 - [Consideraciones generales](#cons_generales)
 - [Datos adiccionales para prevención de fraude](#prevfraudedatosadicionales) 
- [Características](#features) 
 - [Consulta de transacciones](#constrans)
 - [Devoluciones](#devoluciones)
- [Tablas de referencia](#tablas)
- [Versiones disponibles](#availableversions)

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

[Formulario Hibrido](#formHibrido).
<a name="formHibrido"></a>
####Formulario Híbrido
En la versión 1.7 del Plugin se incluyen dos tipos de formularios de pago, redirección y Formulario Híbrido (embebido en el e commerce). Para utilizar este último se debe seleccionar Híbrido en la configuración geneeral delPlugin. 
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/Selecci%C3%B3n_019.png)

[<sub>Volver a inicio</sub>](#inicio)

<a name="tca"></a>
[Nuevas columnas y atributos](#tca)
Al instalar el PlugIn se creara automáticamente la tabla (prefix)_virtuemart_payment_plg_todopago, para uso internos del mismo.

[Prevencion de Fraude](#cybersource)
<a name="cybersource"></a>
###Consideraciones Generales (para todos los verticales, por defecto RETAIL)
El plug in, toma valores est&aacute;ndar del framework para validar los datos del comprador.
Para acceder a los datos de TodoPago se utilizar el objeto $method que se puede crear de la forma: $method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id).
<br />
Para acceder a los datos del vendedor, productos y carrito se usan los objetos $cart y $order que llegan como parámetro en los métodos en los que se necesitan. 
<br />
```php
'CSBTCITY'=>$cart->BT['city'], //Ciudad de facturación, MANDATORIO.		
'CSBTCUSTOMERID'=>$cart->user->customer_number,
'CSBTIPADDRESS'=>$this->getTodoPagoClientIp(),	
'CSBTEMAIL'=>$cart->BT['email'], 	
'CSBTFIRSTNAME'=>$cart->BT['first_name'] ,		
'CSBTLASTNAME'=>$cart->BT['last_name'], 		
'CSBTPHONENUMBER'=>$cart->BT['phone_1'],		
'CSBTPOSTALCODE'=>$cart->BT['zip'], 
'CSBTSTATE'=>$this->tp_states, 
'CSBTSTREET1'=>$cart->BT['address_1'],
'CSPTGRANDTOTALAMOUNT'=>$cart->cartPrices['billTotal'],                                 
'AMOUNT' => $cart->cartPrices['billTotal']	
```
Los únicos modelos que tenemos que incluir para obtener los datos restantes son 'Customfields', 'currency' e invocar a los métodos staticos de la clase ShopFunctions.<br />

```php
$customFieldsModel = VmModel::getModel ('Customfields');
$customFieldsModel->getCustomEmbeddedProductCustomFields($prod->virtuemart_product_id); 
$currency_model = VmModel::getModel('currency');
$currency = $currency_model->getCurrency($order['details']['BT']->user_currency_id);<br />;
$countryIso = ShopFunctions::getCountryByID($order['details']['BT']->virtuemart_country_id,'country_2_code');
$countryName = ShopFunctions::getCountryByID($order['details']['BT']->virtuemart_country_id);
```
<a name="constrans"></a>
####Consulta de Transacciones
Las consultas On Line de las transacciones se realizan ingresando a la orden. En el recuadro "Consola TodoPago".

<a name="devoluciones"></a>
####Devoluciones
Se pueden realizar devoluciones mediante la "Consola de Todopago", simplemente debe ingresar el monto a devolver al comprador con el siguiente formato: 1.45

En caso que la devolución se realice de manera afirmativa se vera un mensaje como el detallado en la imagen siguiente, en caso contrario se podra ver el motivo del fallo.
![imagen Payment Methods](https://github.com/TodoPago/imagenes/blob/master/virtuemart/Seleccion_004.png)

####Muy Importante
<strong>Provincias:</strong> Al ser un campo MANDATORIO para enviar y propio del plugin este campo se completa por parte del usuario al momento del check out.
<br />
[<sub>Volver a inicio</sub>](#inicio)
