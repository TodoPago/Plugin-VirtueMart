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
    if($refunds != null){  
        $aux = 'REFUND'; 
        $auxColection = 'REFUNDS';
    }


    if (isset($status['Operations']) && is_array($status['Operations']) ) {
        foreach ($status['Operations'] as $key => $value) {   
          if(is_array($value) && $key == $auxColection){
              $rta .= "<tr><td>$key: </td>\n";
              foreach ($auxArray[$aux] as $key2 => $value2) {  
                  $rta .= '<td>';           
                  $rta .= $aux." \n";                
                  if(is_array($value2)){                    
                      foreach ($value2 as $key3 => $value3) {
                          if(is_array($value3)){ 
                              foreach ($value3 as $key4 => $value4) {
                                  $rta .= "   - $key4: $value4 \n";
                              }
                          }else{
                              $rta .= "   - $key3: $value3 \n"; 
                          }                   
                      }
                  }else{
                    $rta .= "   - $key2: $value2 \n";
                  }
                  $rta .= '<td>';
              }
              $rta .= "</tr>";                                
          }else{
              if(is_array($value)){
                  $rta .= "<tr><td>$key:</td><td>";
                  foreach ($value as $key5 => $value5) {
                      $rta .= "   - $key5: $value5 \n";
                  }
                  $rta .= "</td></tr>";
              }else{
                  $rta .= "<tr><td>$key:</td><td>$value</td></tr>";
              }
          }
        }
    }else{
        $rta .= '<tr><td>No hay operaciones para esta orden.<td></tr>';
    }

    echo $rta;
?>
    </table>