<?php 

$states = Helper::getTPStates();
            $states_html = '<select name="tp_states" id="tp_states">';
            foreach($states as $city => $code){

                $states_html.= '<option value="'.$code.'">'.$city.'</option>';

            }

            $states_html.= '</select>';


            $html .= '<br /><br />
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr valign="top" style="border:none">
			<td nowrapalign="left" >
			<img src="'.JURI::root().'plugins/vmpayment/todopago/logo.jpg" />
			<br /><br />
			<label for="cc_type">Elige la provincia</label>
			'.$states_html.'</td>
			</tr>
			</table><br />';
