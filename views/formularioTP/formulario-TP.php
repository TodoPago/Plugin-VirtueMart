
<?php $_css =  JURI::base()."plugins/vmpayment/todopago/views/formularioTP/styles.css"; ?>

<?php $_corner = JURI::base()."plugins/vmpayment/todopago/views/formularioTP/js/jquery.corner.js"; ?><!--script src="TPHybridForm-v0.1.js"></script-->
		<?php if ($ambiente == "test") {?>
		<script src="https://developers.todopago.com.ar/resources/TPHybridForm-v0.1.js"></script>
		<?php } else {?>
		<script src="https://forms.todopago.com.ar/resources/TPHybridForm-v0.1.js"></script>
		
		<?php }?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $_corner ?>"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo $_css ?>">
		<script type="text/javascript">

			$(document).ready(function() {
				$("#breadcrumbs").hide();

				$(".vm-order-done").hide();
			    $(':input').corner("round 3px");
			    
			    $("#formaDePagoCbx").change(function () {
				    if(this.value == 500 || this.value == 501){
				    	$(".spacer").hide();
				    }else{
				    	$(".spacer").show();
				    }
				})
			});

		</script>
<br><br>	
	<body class="contentContainer">
		<div id="tp-form-tph">
			<div id="tp-logo"></div>
			<div id="tp-content-form">
				<span class="tp-label">Elegí tu forma de pago </span>
				<div>
					<select id="formaDePagoCbx"></select>	
				</div>
				<div>
					<select id="bancoCbx"></select>
				</div>
				<div>
					<select id="promosCbx" class="left"></select>
					<label id="labelPromotionTextId" class="left tp-label"></label>
					<div class="clear"></div>
				</div>
				<div>
					<input class="inputbox" id="numeroTarjetaTxt"/>
				</div>
				<div class="dateFields">
		            <input id="mesTxt" class="left">
		            
		            <input id="anioTxt" class="left">
		            <div class="clear"></div>
		      	</div>
				<div>
					<input width="100%" class="inputbox" id="codigoSeguridadTxt" class="left"/>
					<label id="labelCodSegTextId" class="left tp-label"></label>
					<div class="clear"></div>
				</div>
				<div>
					<input class="inputbox" id="apynTxt"/>
				</div>
				<div>
					<select id="tipoDocCbx"></select>
				</div>
				<div>
					<input class="inputbox" id="nroDocTxt"/>	
				</div>
				<div>
					<input class="inputbox" id="emailTxt"/><br/>
				</div>
				<div id="tp-bt-wrapper">
					<button id="MY_btnConfirmarPago" class="tp-button"/>
					<!--button id="MY_btnPagarConBilletera" class="tp-button"/-->
				</div>
			</div>	
		</div>

	</body>
	<script>
		//securityRequesKey, esta se obtiene de la respuesta del SAR
		var security = '<?php echo $rta["PublicRequestKey"]; ?>';
		var mail = "<?php echo $data_operation['CSSTEMAIL'] ?>";
		var completeName = "<?php echo $data_operation['CSBTLASTNAME'].' '.$data_operation['CSBTFIRSTNAME'] ?>";
		


		/************* CONFIGURACION DEL API ************************/
		window.TPFORMAPI.hybridForm.initForm({
			callbackValidationErrorFunction: 'validationCollector',
            callbackBilleteraFunction: 'billeteraPaymentResponse',
            callbackCustomSuccessFunction: 'customPaymentSuccessResponse',
            callbackCustomErrorFunction: 'customPaymentErrorResponse',
            botonPagarId: 'MY_btnConfirmarPago',
            botonPagarConBilleteraId: 'MY_btnPagarConBilletera',
            modalCssClass: 'modal-class',
            modalContentCssClass: 'modal-content',
            beforeRequest: 'initLoading',
            afterRequest: 'stopLoading'
		});

		/************* SETEO UN ITEM PARA COMPRAR ************************/
        window.TPFORMAPI.hybridForm.setItem({
            publicKey: security,
            defaultNombreApellido: completeName,
            //defaultNumeroDoc: dni,
            defaultMail: mail,
            //defaultTipoDoc: defDniType
        });
		
		//callbacks de respuesta del pago
		function validationCollector(parametros) {
			console.log("My validator collector");
			console.log(parametros.field + " ==> " + parametros.error);
			console.log(parametros);
		}
		function billeteraPaymentResponse(response) {
			console.log("My wallet callback");
			console.log(response.ResultCode + " : " + response.ResultMessage);
			console.log(response);
		}
		function customPaymentSuccessResponse(response) {
			console.log("My custom payment success callback");
			console.log(response.ResultCode + " : " + response.ResultMessage);
			console.log(response);
			console.log("aca redirijo a :<?php echo $data_comercial['URL_OK'] ?>&Answer="+response.AuthorizationKey);
			url_ok = "<?php echo $data_comercial['URL_OK'] ?>&Answer="+response.AuthorizationKey;
			window.location.href = url_ok;
		}
		
		function customPaymentErrorResponse(response) {
			console.log("Mi custom payment error callback");
			console.log(response.ResultCode + " : " + response.ResultMessage);
			console.log(response);
			url_error = "<?php echo $data_comercial['URL_ERROR'] ?>&Answer="+response.AuthorizationKey;
			window.location.href = url_error;
		}
		function initLoading() {
			console.log('Cargando');
		}
		function stopLoading() {
			console.log('Stop loading...');
		}

	</script>
