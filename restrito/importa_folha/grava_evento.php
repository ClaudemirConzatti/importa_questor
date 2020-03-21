<?php

  include('../../class/connect.php');
  ini_set('display_errors',0);

  $empresa  		= $_POST['empresa'];
  $CodEvento 		= $_POST['CodEvento'];	
  $Evento 			= utf8_decode($_POST['Evento']);	
  $Tipo 			  = utf8_decode($_POST['Tipo']);
  $lancar 			= $_POST['lancar'];		
  $CodConta 		= $_POST['CodConta'];	
  $CodCC 			  = $_POST['CodCC'];	
  
  if($Tipo==utf8_decode('DÉBITO')){$Multiplicador=-1;}else{$Multiplicador=1;};

  $sql = "SELECT * FROM eventos_rh.eventos WHERE Empresa= $empresa AND CodEvento = $CodEvento";
  $y=mysqli_query($con_mysql,$sql);
  $rowcount=mysqli_num_rows($y);
  echo $sql.'<br>';
  if($rowcount==0){
    $sql = "INSERT INTO eventos_rh.eventos ( Empresa, CodEvento, Evento, Tipo, Multiplicador, lancar, CodConta, CodCC)
                VALUES ('$empresa', $CodEvento, '$Evento', '$Tipo', $Multiplicador, '$lancar', $CodConta, $CodCC)";
  }else{
    $sql = "UPDATE eventos_rh.eventos SET 
                CodEvento = $CodEvento
                , Evento='$Evento' 
                , Tipo='$Tipo'
                , Multiplicador=$Multiplicador
                , lancar='$lancar'
                , CodConta=$CodConta
                , CodCC=$CodCC

            WHERE Empresa='$empresa'
              AND CodEvento=$CodEvento";
  }
  $y=mysqli_query($con_mysql,$sql);
  if (!$y) {
    $erro = 1;
  }

echo $erro.$sql;

?>

