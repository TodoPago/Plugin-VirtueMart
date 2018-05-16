<script type="text/javascript">

   //evita el f5
    document.onkeydown = function(event){
        if(event.keyCode==116){
            return false;
          }
    }


</script>
<table class="adminlist table ">
    <thead>
        <tr>
            <th colspan="2">
                Consola de TodoPago
            </th>
        </tr>
    </thead>
    <tr><td>
    <img src="https://portal.todopago.com.ar/app/images/logo.png" alt="Todopago"/>
    <h3>Estado de la operacion - TodoPago </h3>
    </td><td></td>
    </tr>
<?php
    $rta = '';
    $refunds = $status['Operations']['REFUNDS'];

    $auxArray = array(
           "REFUND" => $refunds
           );
    $auxColection  = '';
    function printGetStatus($arrayResult, $indent = 0) {
    	$rta = '';
    	foreach ($arrayResult as $key => $value) {
    	    if ($key !== 'nil' && $key !== "@attributes") {
    			if (is_array($value) ){
    			    $rta .= "<tr>";
    			    $rta .= "<td>".str_repeat("-", $indent) . "<strong>$key:</strong></td>";
    			    $rta .= "<td>".printGetStatus($value, $indent + 2)."</td>";
    			    $rta .= "</tr>";
    			} else {
    			    $rta .= "<tr><td>".str_repeat("-", $indent) . "<strong>$key:</strong></td><td> $value </td></tr>";
    			}
    	    }
    	}
    	return $rta;
    }
    if($refunds != null){
        $aux = 'REFUND';
        $auxColection = 'REFUNDS';
    }


    if (isset($status['Operations']) && is_array($status['Operations']) ) {
        $rta .= printGetStatus($status['Operations']);
    }else{
        $rta .= '<tr><td>No hay operaciones para esta orden.<td></tr>';
    }

    echo $rta;
?>
    </table>
