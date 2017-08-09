<a name="inicio"></a>
VIRTUEMART
==========

Plug in para la integración con gateway de pago <strong>Todo Pago</strong>
- [Consideraciones Generales](#consideracionesgenerales)
- [Instalación](#instalacion)
- [Configuración](#configuracion)
- [Configuración plug in](#confplugin)
	- [Configuracion de Logs](#conflog)
	- [Obtener datos de configuracion](#getcredentials)
	- [Formulario Hibrido](#formHibrido)
	- [Configuración de Maximo de Cuotas](#maxcuotas)
	- [Configuración de tiempo de vida del formulario de pago](#timeout)
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

<br />
[<sub>Volver a inicio</sub>](#inicio)

<a name="configuracion"></a>
## Configuración

<a name="confplugin"></a>
#### Configuración plug in
Para llegar al menu de configuración ir a: <em>Virtuemart -> Payment Methods -> New</em>, completar el formulario y elegir TodoPago entre los medios de pago y grabar<br />
Al grabar dirigirse a la tab Configuration y completar los datos de conexión y usuario de TodoPago.
<br />
<sub><em>Payment Methods</em></sub>
![imagen Payment Methods](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/payment-methods-1.png)

![imagen Payment Methods](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/payment-methods-2.png)

<br />
[<sub>Volver a inicio</sub>](#inicio)
<br />

[Configuracion de Logs](#conflog).
<a name="conflog"></a>
####Configuracion de Logs
Los logs del comercio realizado por los compradores tal como la autorizacion de compra (sendAuthorizeRequest) y confirmacion (GetAuthorizeRequest) se harán en el directorio de logs por defecto de joomla que es la carpeta logs/ con el nombre de todopago.0.log.php o todopago.log.
Para los logs que se realizan desde el lado del administrador , tal como sucede al hacer una devolucion (voidRequest y returnRequest), Virtuemart pide que especifique una carpeta ubicada fuera del proyecto por cuestiones de seguridad de informacion. este directorio puede ser por ejemplo: www/vmfiles 
Para realizar esta configuracion:
 - Ir a site->Global Configuration->System 
 - En System settings completar el campo 'Path to Log Folder' con la ruta absoluta de la carpeta Ej: C:\UwAmp\www\vmfiles tal como muestra la imagen y guardar.
 ![imagen configuracion_logs](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/vm_conflog1.PNG) 
 - Ir a VirtueMart->Configuration->Templates y completar el campo Safe Path con la ruta especificada anteriormente y guardar 
 ![imagen configuracion_logs](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/vm_conflog2.PNG) 
 - Luego de esto ya estara configurada carpeta de logs para el administrador del comercio.
<br />
[<sub>Volver a inicio</sub>](#inicio)
<br />


[Obtener datos de configuracion](#getcredentials).
<a name="getcredentials"></a>
####Obtener datos de configuracion
En el popup loguearse con el mail y password de Todopago. Los datos se cargaran automaticamente en los campos Authorization HTTP y Id Site Todo Pago code en el ambiente correspondiente (Desarrollo o produccion ) y solo hay que hacer click en el boton guardar datos y listo.
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/virtue_credentials.png)

[<sub>Volver a inicio</sub>](#inicio)

[Formulario Hibrido](#formHibrido).
<a name="formHibrido"></a>
#### Formulario Híbrido
En la versión 1.7 del Plugin se incluyen dos tipos de formularios de pago, redirección y Formulario Híbrido (embebido en el e commerce). Para utilizar este último se debe seleccionar Híbrido en la configuración geneeral delPlugin. 
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/Selecci%C3%B3n_019.png)

** Boton Pagar con Billetera ** En el formulario hibrido aparecera la posibilidad de utilizar la billetera virtual de todopago en un boton que se llama "Boton Pagar con Billetera"

[<sub>Volver a inicio</sub>](#inicio)
</br>

<a name="maxcuotas"></a>
####Configuración de Maximo de Cuotas
Se puede configurar la cantidad máxima de cuotas que ofrecerá el formulario de TodoPago con el campo cantidad máxima de cuotas. Para que se tenga en cuenta este valor se debe habilitar el campo Habilitar máximo de cuotas y tomará el valor fijado para máximo de cuotas. En caso que esté habilitado el campo y no haya un valor puesto para las cuotas se tomará el valor 12 por defecto.
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/virtue-maxinstallments.png)
</br>
[<sub>Volver a inicio</sub>](#inicio)

</br>

<a name="timeout"></a>
####Configuración de tiempo de vida del formulario
En la configuracion del plugin se puede setear el tiempo maximo en el que se puede realizar el pago del formulario en milisegundos. Por defecto si no se envia, 1800000 (30 minutos)
Valor minimo: 300000 (5 minutos)
Valor maximo: 21600000 (6hs)
![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/virtuemart/virtue-timeout.png)</br>
<br />
[<sub>Volver a inicio</sub>](#inicio)

</br>








<a name="tca"></a>
[Nuevas columnas y atributos](#tca)
Al instalar el PlugIn se creara automáticamente la tabla (prefix)_virtuemart_payment_plg_todopago, para uso internos del mismo.

<a name="cybersource"></a>
## Control de Fraude

<a name="cons_generales"></a>
### Consideraciones Generales (para todos los verticales, por defecto RETAIL)
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
#### Consulta de Transacciones
Las consultas On Line de las transacciones se realizan ingresando a la orden. En el recuadro "Consola TodoPago".

<a name="devoluciones"></a>
#### Devoluciones
Se pueden realizar devoluciones mediante la "Consola de Todopago", simplemente debe ingresar el monto a devolver al comprador con el siguiente formato: 1.45

En caso que la devolución se realice de manera afirmativa se vera un mensaje como el detallado en la imagen siguiente, en caso contrario se podra ver el motivo del fallo.
![imagen Payment Methods](https://github.com/TodoPago/imagenes/blob/master/virtuemart/Seleccion_004.png)

#### Muy Importante
<strong>Provincias:</strong> Al ser un campo MANDATORIO para enviar y propio del plugin este campo se completa por parte del usuario al momento del check out.
[<sub>Volver a inicio</sub>](#inicio)

<a name="codigoerrores"></a>
## Tabla de errores operativos

<table>
<tr><th>Id mensaje</th><th>Mensaje</th></tr>
<tr><td>-1</td><td>Aprobada.</td></tr>
<tr><td>1100</td><td>El monto ingresado es menor al mínimo permitido</td></tr>
<tr><td>1101</td><td>El monto ingresado supera el máximo permitido.</td></tr>
<tr><td>1102</td><td>Tu tarjeta no corresponde con el banco seleccionado. Iniciá nuevamente la compra.</td></tr>
<tr><td>1104</td><td>El precio ingresado supera al máximo permitido.</td></tr>
<tr><td>1105</td><td>El precio ingresado es menor al mínimo permitido.</td></tr>
<tr><td>1070</td><td>El plazo para realizar esta devolución caducó.</td></tr>
<tr><td>1081</td><td>El saldo de tu cuenta es insuficiente para realizar esta devolución.</td></tr>
<tr><td>2010</td><td>En este momento la operación no pudo ser realizada. Por favor intentá más tarde. Volver a Resumen.</td></tr>
<tr><td>2031</td><td>En este momento la validación no pudo ser realizada, por favor intentá más tarde.</td></tr>
<tr><td>2050</td><td>Tu compra no puede ser realizada. Comunicate con tu vendedor.</td></tr>
<tr><td>2051</td><td>Tu compra no pudo ser procesada. Comunicate con tu vendedor.</td></tr>
<tr><td>2052</td><td>Tu compra no pudo ser procesada. Comunicate con tu vendedor. </td></tr>
<tr><td>2053</td><td>Tu compra no pudo ser procesada. Comunicate con tu vendedor.</td></tr>
<tr><td>2054</td><td>El producto que querés comprar se encuentra agotado. Por favor contactate con tu vendedor.</td></tr>
<tr><td>2056</td><td>Tu compra no pudo ser procesada.</td></tr>
<tr><td>2057</td><td>La operación no pudo ser procesada. Por favor intentá más tarde.</td></tr>
<tr><td>2058</td><td>La operación fué rechazada. Comunicate con el 0800 333 0010.</td></tr>
<tr><td>2059</td><td>La operación no pudo ser procesada. Por favor intentá más tarde.</td></tr>
<tr><td>2062</td><td>Tu compra no puede ser realizada. Comunicate con tu vendedor.</td></tr>
<tr><td>2064</td><td>Tu compra no puede ser realizada. Comunicate con tu vendedor.</td></tr>
<tr><td>2074</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>2075</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>2076</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>90000</td><td>La cuenta destino de los fondos es inválida. Verificá la información ingresada en Mi Perfil.</td></tr>
<tr><td>90001</td><td>La cuenta ingresada no pertenece al CUIT/ CUIL registrado.</td></tr>
<tr><td>90002</td><td>No pudimos validar tu CUIT/CUIL.  Comunicate con nosotros <a href="#contacto" target="_blank">acá</a> para más información.</td></tr>
<tr><td>99900</td><td>Tu compra fue exitosa.</td></tr>
<tr><td>99901</td><td>Tu Billetera Virtual no tiene medios de pago adheridos. Ingresá a tu cuenta de Todo Pago y cargá tus tarjetas.</td></tr>
<tr><td>99902</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99903</td><td>Lo sentimos, hubo un error al procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99904</td><td>El saldo de tu tarjeta no te permite realizar esta compra. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99905</td><td>En este momento la operación no pudo ser procesada. Intentá nuevamente.</td></tr>
<tr><td>99907</td><td>Tu compra no pudo ser procesada. Comunicate con tu vendedor. </td></tr>
<tr><td>99910</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99911</td><td>Lo sentimos, se terminó el tiempo para confirmar esta compra. Por favor iniciá nuevamente el proceso de pago.</td></tr>
<tr><td>99950</td><td>Tu compra no pudo ser procesada.</td></tr>
<tr><td>99960</td><td>Esta compra requiere autorización de VISA. Comunicate al número que se encuentra al dorso de tu tarjeta.</td></tr>
<tr><td>99961</td><td>Esta compra requiere autorización de AMEX. Comunicate al número que se encuentra al dorso de tu tarjeta.</td></tr>
<tr><td>99970</td><td>Lo sentimos, no pudimos procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99971</td><td>Lo sentimos, no pudimos procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99972</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago. </td></tr>
<tr><td>99974</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago. </td></tr>
<tr><td>99975</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago. </td></tr>
<tr><td>99977</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago. </td></tr>
<tr><td>99979</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago. </td></tr>
<tr><td>99978</td><td>Lo sentimos, no pudimos procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99979</td><td>Lo sentimos, el pago no pudo ser procesado.</td></tr>
<tr><td>99980</td><td>Ya realizaste una compra por el mismo importe. Iniciala nuevamente en unos minutos.</td></tr>
<tr><td>99982</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando.</td></tr>
<tr><td>99983</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99984</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99985</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99986</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99987</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99988</td><td>Tu compra no pudo ser procesada. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99989</td><td>Tu tarjeta no autorizó tu compra. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99990</td><td>Tu tarjeta está vencida. Iniciá nuevamente la compra utilizando otro medio de pago.</td></tr>
<tr><td>99991</td><td>Los datos informados son incorrectos. Por favor ingresalos nuevamente.</td></tr>
<tr><td>99992</td><td>El saldo de tu tarjeta no te permite realizar esta compra. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99993</td><td>Tu tarjeta no autorizó el pago. Iniciá nuevamente la compra utilizando otro medio de pago.</td></tr>
<tr><td>99994</td><td>El saldo de tu tarjeta no te permite realizar esta operacion.</td></tr>
<tr><td>99995</td><td>Tu tarjeta no autorizó tu compra. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
<tr><td>99996</td><td>La operación fué rechazada por el medio de pago porque el monto ingresado es inválido.</td></tr>
<tr><td>99997</td><td>Lo sentimos, en este momento la operación no puede ser realizada. Por favor intentá más tarde.</td></tr>
<tr><td>99998</td><td>Tu tarjeta no autorizó tu compra. Iniciala nuevamente utilizando otro medio de pago.
<tr><td>99999</td><td>Tu compra no pudo realizarse. Iniciala nuevamente utilizando otro medio de pago.</td></tr>
</table>

[<sub>Volver a inicio</sub>](#inicio)

<a name="interrores"></a>
## Tabla de errores de integración

<table>
<tr><td>**Id mensaje**</td><td>**Descripción**</td></tr>
<tr><td>98001 </td><td>ERROR: El campo CSBTCITY es requerido</td></tr>
<tr><td>98002 </td><td>ERROR: El campo CSBTCOUNTRY es requerido</td></tr>
<tr><td>98003 </td><td>ERROR: El campo CSBTCUSTOMERID es requerido</td></tr>
<tr><td>98004 </td><td>ERROR: El campo CSBTIPADDRESS es requerido</td></tr>
<tr><td>98005 </td><td>ERROR: El campo CSBTEMAIL es requerido</td></tr>
<tr><td>98006 </td><td>ERROR: El campo CSBTFIRSTNAME es requerido</td></tr>
<tr><td>98007 </td><td>ERROR: El campo CSBTLASTNAME es requerido</td></tr>
<tr><td>98008 </td><td>ERROR: El campo CSBTPHONENUMBER es requerido</td></tr>
<tr><td>98009 </td><td>ERROR: El campo CSBTPOSTALCODE es requerido</td></tr>
<tr><td>98010 </td><td>ERROR: El campo CSBTSTATE es requerido</td></tr>
<tr><td>98011 </td><td>ERROR: El campo CSBTSTREET1 es requerido</td></tr>
<tr><td>98012 </td><td>ERROR: El campo CSBTSTREET2 es requerido</td></tr>
<tr><td>98013 </td><td>ERROR: El campo CSPTCURRENCY es requerido</td></tr>
<tr><td>98014 </td><td>ERROR: El campo CSPTGRANDTOTALAMOUNT es requerido</td></tr>
<tr><td>98015 </td><td>ERROR: El campo CSMDD7 es requerido</td></tr>
<tr><td>98016 </td><td>ERROR: El campo CSMDD8 es requerido</td></tr>
<tr><td>98017 </td><td>ERROR: El campo CSMDD9 es requerido</td></tr>
<tr><td>98018 </td><td>ERROR: El campo CSMDD10 es requerido</td></tr>
<tr><td>98019 </td><td>ERROR: El campo CSMDD11 es requerido</td></tr>
<tr><td>98020 </td><td>ERROR: El campo CSSTCITY es requerido</td></tr>
<tr><td>98021 </td><td>ERROR: El campo CSSTCOUNTRY es requerido</td></tr>
<tr><td>98022 </td><td>ERROR: El campo CSSTEMAIL es requerido</td></tr>
<tr><td>98023 </td><td>ERROR: El campo CSSTFIRSTNAME es requerido</td></tr>
<tr><td>98024 </td><td>ERROR: El campo CSSTLASTNAME es requerido</td></tr>
<tr><td>98025 </td><td>ERROR: El campo CSSTPHONENUMBER es requerido</td></tr>
<tr><td>98026 </td><td>ERROR: El campo CSSTPOSTALCODE es requerido</td></tr>
<tr><td>98027 </td><td>ERROR: El campo CSSTSTATE es requerido</td></tr>
<tr><td>98028 </td><td>ERROR: El campo CSSTSTREET1 es requerido</td></tr>
<tr><td>98029 </td><td>ERROR: El campo CSMDD12 es requerido</td></tr>
<tr><td>98030 </td><td>ERROR: El campo CSMDD13 es requerido</td></tr>
<tr><td>98031 </td><td>ERROR: El campo CSMDD14 es requerido</td></tr>
<tr><td>98032 </td><td>ERROR: El campo CSMDD15 es requerido</td></tr>
<tr><td>98033 </td><td>ERROR: El campo CSMDD16 es requerido</td></tr>
<tr><td>98034 </td><td>ERROR: El campo CSITPRODUCTCODE es requerido</td></tr>
<tr><td>98035 </td><td>ERROR: El campo CSITPRODUCTDESCRIPTION es requerido</td></tr>
<tr><td>98036 </td><td>ERROR: El campo CSITPRODUCTNAME es requerido</td></tr>
<tr><td>98037 </td><td>ERROR: El campo CSITPRODUCTSKU es requerido</td></tr>
<tr><td>98038 </td><td>ERROR: El campo CSITTOTALAMOUNT es requerido</td></tr>
<tr><td>98039 </td><td>ERROR: El campo CSITQUANTITY es requerido</td></tr>
<tr><td>98040 </td><td>ERROR: El campo CSITUNITPRICE es requerido</td></tr>
<tr><td>98101 </td><td>ERROR: El formato del campo CSBTCITY es incorrecto</td></tr>
<tr><td>98102 </td><td>ERROR: El formato del campo CSBTCOUNTRY es incorrecto</td></tr>
<tr><td>98103 </td><td>ERROR: El formato del campo CSBTCUSTOMERID es incorrecto</td></tr>
<tr><td>98104 </td><td>ERROR: El formato del campo CSBTIPADDRESS es incorrecto</td></tr>
<tr><td>98105 </td><td>ERROR: El formato del campo CSBTEMAIL es incorrecto</td></tr>
<tr><td>98106 </td><td>ERROR: El formato del campo CSBTFIRSTNAME es incorrecto</td></tr>
<tr><td>98107 </td><td>ERROR: El formato del campo CSBTLASTNAME es incorrecto</td></tr>
<tr><td>98108 </td><td>ERROR: El formato del campo CSBTPHONENUMBER es incorrecto</td></tr>
<tr><td>98109 </td><td>ERROR: El formato del campo CSBTPOSTALCODE es incorrecto</td></tr>
<tr><td>98110 </td><td>ERROR: El formato del campo CSBTSTATE es incorrecto</td></tr>
<tr><td>98111 </td><td>ERROR: El formato del campo CSBTSTREET1 es incorrecto</td></tr>
<tr><td>98112 </td><td>ERROR: El formato del campo CSBTSTREET2 es incorrecto</td></tr>
<tr><td>98113 </td><td>ERROR: El formato del campo CSPTCURRENCY es incorrecto</td></tr>
<tr><td>98114 </td><td>ERROR: El formato del campo CSPTGRANDTOTALAMOUNT es incorrecto</td></tr>
<tr><td>98115 </td><td>ERROR: El formato del campo CSMDD7 es incorrecto</td></tr>
<tr><td>98116 </td><td>ERROR: El formato del campo CSMDD8 es incorrecto</td></tr>
<tr><td>98117 </td><td>ERROR: El formato del campo CSMDD9 es incorrecto</td></tr>
<tr><td>98118 </td><td>ERROR: El formato del campo CSMDD10 es incorrecto</td></tr>
<tr><td>98119 </td><td>ERROR: El formato del campo CSMDD11 es incorrecto</td></tr>
<tr><td>98120 </td><td>ERROR: El formato del campo CSSTCITY es incorrecto</td></tr>
<tr><td>98121 </td><td>ERROR: El formato del campo CSSTCOUNTRY es incorrecto</td></tr>
<tr><td>98122 </td><td>ERROR: El formato del campo CSSTEMAIL es incorrecto</td></tr>
<tr><td>98123 </td><td>ERROR: El formato del campo CSSTFIRSTNAME es incorrecto</td></tr>
<tr><td>98124 </td><td>ERROR: El formato del campo CSSTLASTNAME es incorrecto</td></tr>
<tr><td>98125 </td><td>ERROR: El formato del campo CSSTPHONENUMBER es incorrecto</td></tr>
<tr><td>98126 </td><td>ERROR: El formato del campo CSSTPOSTALCODE es incorrecto</td></tr>
<tr><td>98127 </td><td>ERROR: El formato del campo CSSTSTATE es incorrecto</td></tr>
<tr><td>98128 </td><td>ERROR: El formato del campo CSSTSTREET1 es incorrecto</td></tr>
<tr><td>98129 </td><td>ERROR: El formato del campo CSMDD12 es incorrecto</td></tr>
<tr><td>98130 </td><td>ERROR: El formato del campo CSMDD13 es incorrecto</td></tr>
<tr><td>98131 </td><td>ERROR: El formato del campo CSMDD14 es incorrecto</td></tr>
<tr><td>98132 </td><td>ERROR: El formato del campo CSMDD15 es incorrecto</td></tr>
<tr><td>98133 </td><td>ERROR: El formato del campo CSMDD16 es incorrecto</td></tr>
<tr><td>98134 </td><td>ERROR: El formato del campo CSITPRODUCTCODE es incorrecto</td></tr>
<tr><td>98135 </td><td>ERROR: El formato del campo CSITPRODUCTDESCRIPTION es incorrecto</td></tr>
<tr><td>98136 </td><td>ERROR: El formato del campo CSITPRODUCTNAME es incorrecto</td></tr>
<tr><td>98137 </td><td>ERROR: El formato del campo CSITPRODUCTSKU es incorrecto</td></tr>
<tr><td>98138 </td><td>ERROR: El formato del campo CSITTOTALAMOUNT es incorrecto</td></tr>
<tr><td>98139 </td><td>ERROR: El formato del campo CSITQUANTITY es incorrecto</td></tr>
<tr><td>98140 </td><td>ERROR: El formato del campo CSITUNITPRICE es incorrecto</td></tr>
<tr><td>98201 </td><td>ERROR: Existen errores en la información de los productos</td></tr>
<tr><td>98202 </td><td>ERROR: Existen errores en la información de CSITPRODUCTDESCRIPTION los productos</td></tr>
<tr><td>98203 </td><td>ERROR: Existen errores en la información de CSITPRODUCTNAME los productos</td></tr>
<tr><td>98204 </td><td>ERROR: Existen errores en la información de CSITPRODUCTSKU los productos</td></tr>
<tr><td>98205 </td><td>ERROR: Existen errores en la información de CSITTOTALAMOUNT los productos</td></tr>
<tr><td>98206 </td><td>ERROR: Existen errores en la información de CSITQUANTITY los productos</td></tr>
<tr><td>98207 </td><td>ERROR: Existen errores en la información de CSITUNITPRICE de los productos</td></tr>
</table>

[<sub>Volver a inicio</sub>](#inicio)
