
<?php $_css_path =  JURI::base()."plugins/vmpayment/todopago/views/formularioTP/css"; ?>
<?php $form_dir =  JURI::base()."plugins/vmpayment/todopago/views/formularioTP"; ?>


	<?php 
		

	if ($ambiente == "test") {
		$env_url = 'https://developers.todopago.com.ar/resources/v2/TPBSAForm.min.js';
	} else {
		$env_url = 'https://forms.todopago.com.ar/resources/v2/TPBSAForm.min.js'; 
	}

	?>

	<script src="<?php echo $env_url; ?>"></script>

	<link href="<?php echo "$_css_path/grid.css"; ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo "$_css_path/form_todopago.css"; ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo "$_css_path/queries.css"; ?>" rel="stylesheet" type="text/css">
	<script src="<?php echo "$_css_path/jquery-3.2.1.min.js"; ?>"></script>

	<body class="contentContainer">

		<div class="progress">
			<div class="progress-bar progress-bar-striped active" id="loading-hibrid">
			</div>
		</div>

		

		<div class="tp_wrapper" id="tpForm">

			<div class="header_info">
		        <div class="bold">Total a pagar $<?php echo $data_operation['AMOUNT']; ?></div>
		        <div>Elegí tu forma de pago</div>
		    </div>


			<section class="billetera_virtual_tp">
				<div class="tp_row tp-flex">
				    <div class="tp_col tp_span_1_of_2 texto_billetera_virtual text_size_billetera">
				        <p>Pagá con tu <strong>Billetera Virtual Todo Pago</strong></p>
				        <p>y evitá cargar los datos de tu tarjeta</p>
				    </div>
				    <div class="tp_col tp_span_1_of_2">
				        <button id="btn_Billetera" title="Pagar con Billetera" class="tp_btn tp_btn_sm text_size_billetera">
				            Iniciar Sesi&oacute;n
				        </button>
				    </div>
				</div>
			</section>

			<section class="billeterafm_tp">
				<div class="field field-payment-method">
				    <label for="formaPagoCbx" class="text_small">Forma de Pago</label>
				    <div class="input-box">
				        <select id="formaPagoCbx" class="tp_form_control"></select>
				        <span class="error" id="formaPagoCbxError"></span>
				    </div>
				</div>
			</section>

			<section class="billetera_tp">
				<div class="tp_row">
				    <p>
				        Con tu tarjeta de crédito o débito
				    </p>
				</div>
				<div class="tp_row">
				    <div class="tp_col tp_span_1_of_2" style="height: 100%; overflow:hidden;">
		                <label for="numeroTarjetaTxt" class="text_small">Número de Tarjeta</label>
		                <div>
			                <input id="numeroTarjetaTxt" class="tp_form_control" maxlength="19" 	title="Número de Tarjeta" min-length="14" autocomplete="off" style="height:27px;">
		            	</div>
		                <div style="position:relative;margin-left: 310px;width: 37.983px;margin-top: -39px;border-bottom-width: 0px;border-bottom-style: solid;margin-bottom: 22px;height: 29px;">
			                <img src="<?php echo $form_dir;?>/images/empty.png" id="tp-tarjeta-logo"
		                     alt="" style="width: 33px;" />
		                </div>
		                <!--span class="error" id="numeroTarjetaTxtError"></span-->
		                <span id="numeroTarjetaLbl" class="error"></span>
		            </div>
				    <div class="tp_col tp_span_1_of_2">
				        <label for="bancoCbx" class="text_small">Banco</label>
				        <select id="bancoCbx" class="tp_form_control" placeholder="Selecciona banco"></select>
				        <span class="error" id="bancoCbxError">
				    </div>
				    <div class="tp_col tp_span_1_of_2 payment-method">
				        <label for="medioPagoCbx" class="text_small">Medio de Pago</label>
				        <select id="medioPagoCbx" class="tp_form_control" placeholder="Mediopago"></select>
				        <span class="error" id="medioPagoCbxError"></span>
				    </div>
				</div>

				<section class="tp_row" id="peibox">
				    <div class="tp_row tp_pei" style="height: 100px;">
				        <div class="tp_col tp_span_1_of_2 tp_pei" >
				            <label id="peiLbl" for="peiCbx" class="text_small right">Pago con PEI</label>
				        </div>
				        <label class="switch" id="switch-pei">
				            <input type="checkbox" id="peiCbx">
				            <span class="slider round"></span>
				            <span id="slider-text"></span>
				        </label>
				    </div>
				</section>

				<!--div class="tp_row">
				    <div class="tp_col tp_span_1_of_2">
				        <label for="medioPagoCbx" class="text_small">Medio de Pago</label>
				        <select id="medioPagoCbx" class="tp_form_control" placeholder="Mediopago"></select>
				        <span class="error" id="medioPagoCbxError"></span>
				    </div>
				</div-->

				<div class="tp_row">
				    <div class="tp_col tp_span_1_of_2">
				        <div class="tp_col tp_span_1_of_2">
				            <label for="mesCbx" class="text_small"  style="width: 120%;" >Vencimiento</label>

				            <div class="tp_row">
				                <div class="tp_col tp_span_1_of_2"  >
				                    <select id="mesCbx" maxlength="2" class="tp_form_control" placeholder="Mes"></select>
				                </div>
				                <div class="tp_col tp_span_1_of_2" >
				                    <select id="anioCbx" maxlength="2" class="tp_form_control"></select>
				                </div>
				            	<span id="fechaLbl" class="error"></span>
				            </div>
				        </div>

				        <div class="tp_col tp_span_1_of_3">
				            <label  id="codigoSeguridadTexto" for="codigoSeguridadTxt" class="text_small">Código de Seguridad</label>
				            <input id="codigoSeguridadTxt" class="tp_form_control" maxlength="4" autocomplete="off"/>
				            <span class="error" id="codigoSeguridadTxtError"></span>
				            <label id="codigoSeguridadLbl" class="spacer tp-cvv-lbl"></label>
						</div>


				        <div class="tp-cvv-helper-container">
		                    <div class="tp-anexo clave-ico" id="tp-cvv-caller"></div>
		                    <div id="tp-cvv-helper">
		                        <p>
		                            Para Visa, Master, Cabal y Diners, los 3 dígitos se encuentran en el <strong>dorso</strong>
		                            de
		                            tu tarjeta. (izq)
		                        </p>
		                        <p>
		                            Para Amex, los 4 dígitos se encuentran en el frente de tu tarjeta. (der)
			                    </p>
			                    <img id="tp-cvv-helper-img" alt="ilustración tarjetas" src="<?php echo $form_dir; ?>/images/clave-ej.png">
		                    </div>
		                </div>



				    </div>

				    <div class="tp_col tp_span_1_of_2">
				        <div class="tp_col tp_span_1_of_1">
				            <label for="tipoDocCbx" class="text_small">Tipo</label>
				            <select id="tipoDocCbx" class="tp_form_control"></select>
				        </div>
				        <div class="tp_col tp_span_1_of_2" id="tp-dni-numero" style="width:75.2%;">
				            <label for="NumeroDocCbx" class="text_small">Número</label>
				            <input id="nroDocTxt" maxlength="10" type="text" class="tp_form_control"
				                   autocomplete="off"/>
				            <span class="error" id="nroDocTxtError"></span>
				        </div>
				    </div>
				</div>

				<div class="tp_row">
				    <div class="tp_col tp_span_1_of_2">
				        <label for="nombreTxt" class="text_small">Nombre y Apellido</label>
				        <input id="nombreTxt" class="tp_form_control" autocomplete="off" placeholder="" maxlength="50" style="height: 29px;">
				        <span class="error" id="nombreTxtError"></span>

				    </div>
				    <div class="tp_col tp_span_1_of_2">
				        <label for="emailTxt" class="text_small">Email</label>
				        <input id="emailTxt" type="email" class="tp_form_control" placeholder="nombre@mail.com" data-mail=""
				               autocomplete="off"/><br/>
				        <span class="error" id="emailTxtError"></span>
				    </div>
				</div>

				<div class="tp_row">
				    <div class="tp_col tp_span_1_of_2">
				        <label for="promosCbx" class="text_small">Cantidad de cuotas</label>
				        <select id="promosCbx" class="tp_form_control"></select>
				        <span class="error" id="promosCbxError"></span>
				    </div>
				    <div class="tp_col tp_span_1_of_2" style="height: 38px;margin-top: 0%;padding-top: 42px;" >
				        <div class="clear">
				        	<label id="promosLbl" class="left"></label>
				        </div>
				        <span class="error" id="peiTokenTxtError"></span>
				    </div>
				</div>

				<div class="tp_row">
					<div class="tp_col tp_span_2_of_2" style="height: 124px;">
						<label id="tokenPeiLbl" for="tokenPeiTxt" class="text_small" style="display: inline-block; padding: 10px 10px 10px 2px;"><strong style="font-size:0.9em;">Superaste el monto acumulado utilizando P.E.I.</strong><p style="font-size:0.8em; "><br>Ingresá tu token de seguridad para verificar<br>tu identidad y continuar el pago.<br>Para obtener el token descargá la Aplicación de Todo Pago PEI<br> y seguí los pasos para la activarlo en un cajero Banelco.</p></label>
				    </div>
				    <div class="tp_col tp_span_1_of_2" style="margin-left:0%;">  
				        <input id="tokenPeiTxt" class="tp_form_control tp-tokenpei" style="padding-left: 0%;">
					</div>
				</div>



				<div class="tp_row">
				    <div class="tp_col tp_span_2_of_2" style="margin-top: 6%;">
				        <button id="btn_ConfirmarPago" class="tp_btn" title="Pagar" class="button"><span>Pagar</span></button>
				    </div>
				    <div class="tp_col tp_span_2_of_2">
				        <div class="confirmacion">
				            AL CONFIRMAR EL PAGO ACEPTO LOS <a href="https://www.todopago.com.ar/terminos-y-condiciones-comprador" target="_blank" title="Términos y Condiciones" id="tycId" class="tp_color_text">TÉRMINOS
				            Y CONDICIONES</a> DE TODO PAGO.
				        </div>
				    </div>
				</div>
			</section>

			<div class="tp_row">
				<div id="tp-powered" align="right">
				    Powered by <img id="tp-powered-img" src="<?php echo $form_dir; ?>/images/tp_logo_prod.png"/>
				</div>
			</div>
		</div>

	</body>
	
	<script>
		//var jQuery = $.noConflict();
		var tpformJquery = $.noConflict();
		var urlScript = "<?php echo $env_url; ?>";
		//securityRequesKey, esta se obtiene de la respuesta del SAR
		var urlSuccess = "<?php echo $data_comercial['URL_OK'] ?>";
		var urlError = "<?php echo $data_comercial['URL_ERROR'] ?>";
		var security = '<?php echo $rta["PublicRequestKey"]; ?>';
		var mail = "<?php echo $data_operation['CSSTEMAIL'] ?>";
		var completeName = "<?php echo $data_operation['CSBTLASTNAME'].' '.$data_operation['CSBTFIRSTNAME'] ?>";
		var helperCaller = tpformJquery("#tp-cvv-caller");
		var helperPopup = tpformJquery("#tp-cvv-helper");
		var defDniType = 'DNI';
		var medioDePago = document.getElementById('medioPagoCbx');
	    var tarjetaLogo = document.getElementById('tp-tarjeta-logo');
	    var poweredLogo = document.getElementById('tp-powered-img');
	    var numeroTarjetaTxt = document.getElementById('numeroTarjetaTxt');
	    var poweredLogoUrl = "<?php echo $form_dir;?>/images/";
	    var emptyImg = "<?php echo $form_dir;?>/images/empty.png";
 		var switchPei = tpformJquery("#switch-pei");
	    var sliderText = tpformJquery("#slider-text");
		var peiCbx = tpformJquery("#peiCbx");
	    var idTarjetas = {
	        42: 'VISA',
	        43: 'VISAD',
	        1: 'AMEX',
	        2: 'DINERS',
	        6: 'CABAL',
	        7: 'CABALD',
	        14: 'MC',
	        15: 'MCD'
	    };

		var diccionarioTarjetas = {
			'VISA': 'VISA',
			'VISA DEBITO': 'VISAD',
			'AMEX': 'AMEX',
			'DINERS': 'DINERS',
			'CABAL': 'CABAL',
			'CABAL DEBITO': 'CABALD',
			'MASTER CARD': 'MC',
			'MASTER CARD DEBITO': 'MCD',
			'NARANJA': 'NARANJA'
		};


		/************* HELPERS *************/
		numeroTarjetaTxt.onblur = clearImage;

	    function clearImage() {
	        tarjetaLogo.src = emptyImg;
	    }

		
	    function cardImage(select) {
	        var tarjeta = idTarjetas[select.value];
	        console.log(tarjeta);
	        if (tarjeta === undefined) {
	            tarjeta = diccionarioTarjetas[select.textContent];
	        }
	        if (tarjeta !== undefined) {
	            tarjetaLogo.src = 'https://forms.todopago.com.ar/formulario/resources/images/' + tarjeta + '.png';
	            tarjetaLogo.style.display = 'block';
	        }

	    }



	    /************* SMALL SCREENS DETECTOR (?) *************/
	    function detector() {
	        console.log(tpformJquery("#tp-form").width());
	        var tpFormWidth = tpformJquery("#tp-form").width();
	        if (tpFormWidth < 950) {
	            tpformJquery(".tp-col-right").css("flex-basis", "350px");
	            tpformJquery(".tp-col-left").css("flex-basis", "350px");
	        }
	        if (tpFormWidth < 800) {
	            tpformJquery(".tp-col-right").css("flex-basis", "300px");
	            tpformJquery(".tp-col-left").css("flex-basis", "300px");
	        }
	        if (tpFormWidth < 720) {
	            tpformJquery(".tp-container").css({
	                "margin-left": "0%",
	                "width": "100%",
	                "padding": "5px"
	            });
	            tpformJquery(".left-col").width('100%');
	            tpformJquery(".right-col").width('100%');
	            tpformJquery(".advertencia").css("height", "50px");
	            tpformJquery(".row").css({
	                "height": "60px",
	                "width": "95%",
	                "margin-bottom": "30px"
	            });
	            tpformJquery("#codigo-col").css("margin-bottom", "10px");
	            tpformJquery("#row-pei").css("height", "100px");
	            tpformJquery(".tp-col-left").css("flex-basis", "320px");
	            tpformJquery(".tp-col-right").css("flex-basis", "320px");
	            tpformJquery(".tp-container-2-columns").css({
	                "height": "400px"
	            });
	        }
	        if (tpformJquery("#tp-form").width() < 600) {
	            tpformJquery(".tp-container-2-columns").css({"margin-top": "200px"});
	        }
	    }

	    loadScript(urlScript, function () {
	        loader();
	    });

	    function loadScript(url, callback) {
	        var entorno = (url.indexOf('developers') === -1) ? 'prod' : 'developers';
	        console.log(entorno);
	        poweredLogo.src = poweredLogoUrl + 'tp_logo_' + entorno + '.png';
	        var script = document.createElement("script");
	        script.type = "text/javascript";
	        if (script.readyState) {  //IE
	            script.onreadystatechange = function () {
	                if (script.readyState === "loaded" || script.readyState === "complete") {
	                    script.onreadystatechange = null;
	                    callback();
	                }
	            };
	        } else {  //et al.
	            script.onload = function () {
	                callback();
	            };
	        }
	        script.src = url;
	        document.getElementsByTagName("head")[0].appendChild(script);
	    }

	    function loader() {
	        tpformJquery("#loading-hibrid").css("width", "50%");
	        setTimeout(function () {
	            ignite();
	            tpformJquery(".payment-method").hide();
	            tpformJquery(".billeterafm_tp").hide();
	        }, 100);

	        setTimeout(function () {
	            tpformJquery("#loading-hibrid").css("width", "100%");
	        }, 1000);

	        setTimeout(function () {
	            tpformJquery(".progress").hide('fast');
	        }, 2000);

	        setTimeout(function () {
	            tpformJquery("#tpForm").fadeTo('fast', 1);
	        }, 2200);
	    }

	    //callbacks de respuesta del pago
	    window.validationCollector = function (parametros) {



	        console.log("My validator collector");
	        console.log(parametros);


	        tpformJquery("#peibox").hide();
	        console.log(parametros.field + " ==> " + parametros.error);
	        tpformJquery("#" + parametros.field).addClass("error");
	        var field = parametros.field;
	        input = field.replace(/ /g, "");
	        console.log(input);
	        tpformJquery("#" + input + "Error").html(parametros.error);
	        console.log(parametros); 

	        if (input.search("Txt") !== -1) {
	            label = input.replace("Txt", "Lbl");
	        } else {
	            label = input.replace("Cbx", "Lbl");
	        }
	        if (document.getElementById(label) !== null) {
	            document.getElementById(label).innerText = parametros.error;
	        }

	        //console.log(document.getElementById("codigoSeguridadTxtError").innerText != '');
	        if(document.getElementById("codigoSeguridadTxtError").innerText != ''){
	        	document.getElementById("codigoSeguridadLbl").innerText = '';
	        }





	        ////////////////////////////////

	        /*
			console.log("My validator collector");

			console.log(parametros.field + " -> " + parametros.error);

			tpformJquery("#peibox").hide();

			var input = parametros.field;

			if (input.search("Txt") !== -1) {
				label = input.replace("Txt", "Lbl");
			} else {
				label = input.replace("Cbx", "Lbl");
			}

			if (document.getElementById(label) !== null) {
				document.getElementById(label).innerText = parametros.error;
			}

			*/

	    };

	    function billeteraPaymentResponse(response) {
	        console.log("Iniciando billetera");
	        console.log(response.ResultCode + " -> " + response.ResultMessage);
	        if (response.AuthorizationKey) {
	            window.location.href = urlSuccess + "&Answer=" + response.AuthorizationKey;
	        } else {
	            window.location.href = urlError + "&Error=" + response.ResultMessage;
	        }
	    }

	    function customPaymentSuccessResponse(response) {
	        console.log("Success");
	        console.log(response.ResultCode + " -> " + response.ResultMessage);
	        window.location.href = urlSuccess + "&Answer=" + response.AuthorizationKey;
	    }

	    function customPaymentErrorResponse(response) {
	        console.log(response.ResultCode + " -> " + response.ResultMessage);
	        if (response.AuthorizationKey) {
	            window.location.href = urlError + "&Answer=" + response.AuthorizationKey + "&Error=" + response.ResultMessage;
	        } else {
	            window.location.href = urlError + "&Error=" + response.ResultMessage;
	        }
	    }

	    window.initLoading = function () {
	        console.log("init");
	        cardImage(medioDePago);
	        //tpformJquery("#codigoSeguridadLbl").html("");
	        tpformJquery("#peibox").hide();        

	        tpformJquery("[id*=Error]").html('');

	    };

	    window.stopLoading = function () {
	        console.log('Stop loading...');

	        tpformJquery("#peibox").hide();

	        if (document.getElementById('peiLbl').style.display === "inline-block") {
	        	tpformJquery("label > p").each(function() {
		               var clean_strip = tpformJquery(this).text().replace("<br>","");
		               tpformJquery(this).html(clean_strip);
		           });

	            console.log("visible");
	            tpformJquery("#peibox").show("slow");

	        } else {
	            console.log("invisible");
	            tpformJquery("#peibox").hide("fast");
	            tpformJquery("#peiCbx").prop("checked", false);
	        }

	       // var peiCbx = tpformJquery("#peiCbx");
	        var rowPei = tpformJquery("#row-pei");
	        //tpformJquery.uniform.restore();

	        if (peiCbx.css('display') !== 'none') {
	            activateSwitch(getInitialPEIState());
	        } else {
	            rowPei.css("display", "none");
	            tpformJquery("#peiCbx").prop("checked", false);
	        }
	    };

	    // Verifica que el usuario no haya puesto para solo pagar con PEI y actúa en consecuencia
	    function activateSwitch(soloPEI) {
			readPeiCbx();

			if (!soloPEI) {
			 tpformJquery("#switch-pei").click(function () {
			     console.log("CHECKED", peiCbx.prop("checked"));

			     if (peiCbx.prop("checked") === false) {
			         peiCbx.prop("checked", true);
			        switchPei.prop("checked", false);
			        peiCbx.prop("checked", false); 
			        sliderText.text("NO");
			        sliderText.css('transform', 'translateX(24px)');

			     } else {
			        peiCbx.prop("checked", false);
			        switchPei.prop("checked", true);
			        peiCbx.prop("checked", true);  
			        sliderText.text("SÍ");
			        sliderText.css('transform', 'translateX(3px)');
			     }

			 });
			}

		}
     
	    function readPeiCbx() {
	        if (peiCbx.prop("checked", true)) {
	            switchPei.prop("checked", true);
	            sliderText.text("SÍ");
	            sliderText.css('transform', 'translateX(3px)');
	        } else {
	           switchPei.prop("checked", true);
	           sliderText.text("NO");
	           sliderText.css('transform', 'translateX(24px)');
	       }
	    }

	    function getInitialPEIState() {
	        return (tpformJquery("#peiCbx").prop("disabled"));
	    }

	    tpformJquery('#peiLbl').bind("DOMSubtreeModified", function () {
	        tpformJquery("#peibox").hide();
	    });


	    helperCaller.click(function () {
            helperPopup.toggle(500);
         }
    	);

		function ignite() {
	        /************* CONFIGURACION DEL API ************************/
	        window.TPFORMAPI.hybridForm.initForm({
	            callbackValidationErrorFunction: 'validationCollector',
	            callbackBilleteraFunction: 'billeteraPaymentResponse',
	            callbackCustomSuccessFunction: 'customPaymentSuccessResponse',
	            callbackCustomErrorFunction: 'customPaymentErrorResponse',
	            botonPagarId: 'btn_ConfirmarPago',
	            botonPagarConBilleteraId: 'btn_Billetera',
	            modalCssClass: 'modal-class',
	            modalContentCssClass: 'modal-content',
	            beforeRequest: 'initLoading',
	            afterRequest: 'stopLoading'
	        });

	        /************* SETEO UN ITEM PARA COMPRAR ************************/
	        window.TPFORMAPI.hybridForm.setItem({
	            publicKey: security,
	            defaultNombreApellido: completeName,
	            defaultMail: mail,
	            defaultTipoDoc: defDniType
	        });
	    }


	</script>
<style>
.vm-order-done { display: none; }
</style>
