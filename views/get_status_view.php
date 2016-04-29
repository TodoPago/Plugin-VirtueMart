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
    <?php foreach ($status["Operations"] as $key => $value) { ?>
    
    <tr>
        <td>
            <?php echo $key; ?>
        </td>
        <td> 
            
            <?php if(gettype($value) == "array") {
                echo json_encode($value);
                }else{
                 echo ($value);   
                    }?>
        </td>
    </tr>
    <?php } ?>
    </table>