<?php $_css =  JURI::base()."plugins/vmpayment/todopago/views/formularioTP/styles.css"; ?>

<?php $_corner = JURI::base()."plugins/vmpayment/todopago/views/formularioTP/js/jquery.corner.js"; ?>
		<?php if ($ambiente == "test") {?>
		<script src="https://developers.todopago.com.ar/resources/v2/TPBSAForm.min.js"></script>

		<?php } else {?>
		<script src="https://forms.todopago.com.ar/resources/v2/TPBSAForm.min.js"></script>
		
		<?php }?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $_corner ?>"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo $_css ?>">
		<script type="text/javascript">

			$(document).ready(function() {
				$("#breadcrumbs").hide();
  				$(".vm-order-done").hide();
      			        $(':input').corner("round 3px");
				$("#main").children().last().remove();
			        $(":contains('Your order has been processed.')").last()[0].lastChild.remove();

			    $("#formaDePagoCbx").change(function () {
				    if(this.value == 500 || this.value == 501){
				    	$(".spacer").hide();
				    }else{
				    	$(".spacer").show();
				    }
				});

			    
			});

		</script>
<br><br>	
	<body class="contentContainer">
		<div class="contentContainer main" style="width: auto">
		<div id="tp-form-tph" class="left">
			<div id="tp-logo"></div>
			<div id="tp-content-form fieldset"><br/>
				<div >
					<label for="formaPagoCbx" class="tp-label required">Forma de Pago</label>
					<div >
						<select id="formaPagoCbx" class="select-form " style="height:31px"></select>
					</div>
				</div>

				<div id="fields-card-payment" style="display:none;" >
					<div style="height:91px">
						<label for="numeroTarjetaTxt" class="tp-label required spacer">Número de Tarjeta</label>
						<div class="input-box">
							<input id="numeroTarjetaTxt" class="input-text required-entry"  maxlength="16" title="Número de Tarjeta"/></br>
							<div class="error"  style="color:red;"  id="numeroTarjetaTxtError"></div>
							<label id="numeroTarjetaLbl"  class="error"></label>
						</div>
					</div>
				
					<div >
						<label for="medioPagoCbx" class="tp-label required">Medio de Pago</label>
						<div class="input-box">
							<select id="medioPagoCbx" class="select-form"></select></br>
							<div class="error"  style="color:red;"  id="medioPagoCbxError"></div>
						</div>
					</div>
					<div>
						<label for="bancoCbx" class="tp-label required spacer">Banco Emisor</label>
						<div class="input-box">
							<select id="bancoCbx" class="select-form"></select></br>
							<div class="error"  style="color:red;"  id="bancoCbxError"></div>
						</div>
					</div>

					<div style="height:89px; width:300px">
						<label id="labelPromotionTextId" class="left tp-label " style="width: 420px;">Cantidad de cuotas</label>
						<select id="promosCbx" class="left" style="width: 290px;"></select></br>
						<label id="promosLbl" class="left" style="margin-left: 10px; height:50px"></label><div class="clear"></div>
					</div>

					<div class="field">
						<label id="peiLbl" for="peiCbx" class="tp-label spacer" style="width: 300px;">Pago con PEI</label>
						<div class="input-box">
							<input id="peiCbx" maxlength="16" title="Pago con PEI"/>
						</div>
					</div>

					<div id="ExpirationDate" class="field left dateForm " style="width: 180px;height: 60px;margin-top: 0px;border-left-width: 10px;margin-left: 0px; margin-bottom:27px;" ></br>    
		            	<select id="mesCbx"  maxlength="2" class="left" style="width: 58px;"></select>
			         	<select id="anioCbx"  maxlength="2" class="left" style="width: 58px;"></select></br>
			            <label id="fechaLbl"  style="color:red; font-size:0.8em; margin-top:0px; margin-left: 10px;" ></label>
			            <!--div id="FechadevencimientoError" class="error"></div-->
			            <div class="clear"></div>
			            
			      	</div>
					
					<div class="field" style="width:200px;">
						<input  class="inputbox" id="codigoSeguridadTxt" class="left" style="width: 137px; margin-bottom: 8px;"/></br>
						<label id="codigoSeguridadLbl" class="tp-label" style="font-size:0.8em; margin-top:0px;">Codigo de seguridad</label>
						<div class="error" style="color:red;" id="codigoSeguridadTxtError"></div>
						<div class="clear"></div>
					</div>
					
					<div class="field">
						<label for="nombreTxt" class="tp-label required">Nombre y Apellido</label>
						<div class="input-box">
							<input id="nombreTxt"/></br>
							<div class="error"  style="color:red;" id="nombreTxtError"></div>
						</div>
					</div>
					<div class="field">
						<label for="tipoDocCbx" class="tp-label required spacer">Documento</label>
						<div>
							<div>
								<select id="tipoDocCbx"></select>
							</div>
							<div>
								<input class="inputbox" id="nroDocTxt"/></br>
								<div class="error"  style="color:red;" id="nroDocTxtError"></div>	
							</div>
						</div>
					</div>
					<div class="field">
						<label for="emailTxt" class="tp-label required">Email</label>
						<div class="input-box">
							<input id="emailTxt"/><br/>
							<div class="error"  style="color:red;" id="emailTxtError"></div>
						</div>
					</div>
					<div class="field">
						<label id="tokenPeiLbl" for="tokenPeiTxt" class="tp-label">Token PEI</label>
						<div class="input-box">
							<input id="tokenPeiTxt"/></br>
							<div class="error"  style="color:red;" id="peiTokenTxtError"></div>
						</div>
					</div>
					
				</div>

				<div id="tp-bt-wrapper" style="width: 200px;">
					<button id="MY_btnPagarConBilletera" class="tp-button left"  style="width: 131px;"/>
					<button id="MY_btnConfirmarPago" class="tp-button right"/>
				</div>

			</div>
		</div>

	</div>

	</body>
	<script>
		//securityRequesKey, esta se obtiene de la respuesta del SAR
		var security = '<?php echo $rta["PublicRequestKey"]; ?>';
		var mail = "<?php echo $data_operation['CSSTEMAIL'] ?>";
		var CompleteName = "<?php echo $data_operation['CSBTLASTNAME'].' '.$data_operation['CSBTFIRSTNAME'] ?>";
		
		jQuery("#formaPagoCbx").change(function(){
		    if ( jQuery("#formaPagoCbx").val()==1 ){
		    	jQuery("#fields-card-payment").show();
		    }else{ jQuery("#fields-card-payment").hide(); }
		});
		
		$("#tp-bt-wrapper,#formaPagoCbx,#numeroTarjetaTxt").click(function(e){
			jQuery("input,select").removeClass("error");
			jQuery("span.error").html("");

		});	

		/************* CONFIGURACION DEL API ************************/
		window.TPFORMAPI.hybridForm.initForm({
			callbackValidationErrorFunction: 'validationCollector',
			callbackBilleteraFunction: 'billeteraPaymentResponse',
			botonPagarConBilleteraId: 'MY_btnPagarConBilletera',
			modalCssClass: 'modal-class',
			modalContentCssClass: 'modal-content',
			beforeRequest: 'initLoading',
			afterRequest: 'stopLoading',
			callbackCustomSuccessFunction: 'customPaymentSuccessResponse',
			callbackCustomErrorFunction: 'customPaymentErrorResponse',
			botonPagarId: 'MY_btnConfirmarPago',
			codigoSeguridadTxt: 'Codigo',
		});

		window.TPFORMAPI.hybridForm.setItem({
			publicKey: security,
			//merchantId: merchantId,
			defaultNombreApellido: CompleteName,
			defaultMail: mail,
			//numericAmount: numericAmount
		});
		
		//callbacks de respuesta del pago
		function validationCollector(parametros) {
			console.log("My validator collector");
			console.log(parametros.field + " ==> " + parametros.error);
			jQuery("#"+parametros.field).addClass("error");
			var field = parametros.field;
			field = field.replace(/ /g, "");
			console.log(field);
			jQuery("#"+field+"Error").html(parametros.error);
			//alert(parametros.error);
			console.log(parametros);
		}
		function billeteraPaymentResponse(response) {
			console.log("My wallet callback");
			console.log(response.ResultCode + " : " + response.ResultMessage);
			console.log(response);
			if(response.AuthorizationKey){
			if(response.ResultCode == -1) {
				url_ok = "<?php echo $data_comercial['URL_OK'] ?>&Answer="+response.AuthorizationKey;
				window.location.href = url_ok;
			} else {
				url_error = "<?php echo $data_comercial['URL_ERROR'] ?>&Answer="+response.AuthorizationKey;
				window.location.href = url_error;
			}
			} else{
				url_error = "<?php echo $data_comercial['URL_ERROR'] ?>&Error="+response.ResultMessage;
				window.location.href = url_error;
			}
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
			if(response.AuthorizationKey){
				url_error = "<?php echo $data_comercial['URL_ERROR'] ?>&Answer=" + response.AuthorizationKey;
				window.location.href = url_error;
			} else{
				url_error = "<?php echo $data_comercial['URL_ERROR'] ?>&Error="+response.ResultMessage;
				window.location.href = url_error;
			}
		}
		function initLoading() {
			console.log('Cargando');
		}
		function stopLoading() {
			console.log('Stop loading...');
		}

	</script>
<style>
.vm-order-done { display: none; }
</style>
