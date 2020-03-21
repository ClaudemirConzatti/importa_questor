<?php
  session_start();
  include('../../class/connect.php');
  ini_set('display_errors',0);
  $cod_func_v = '';

  if (isset($_FILES)) {
      $arquivoUpload = $_FILES['file_csv'];
      move_uploaded_file($arquivoUpload['tmp_name'],'uploads/'.$arquivoUpload['name']);
      $arq = 'upload/'.$arquivoUpload['name'];
    };
    echo "<input type='hidden' id='arq' value='$arq'>";
    $handle = fopen ($arq ,'r');


    while (($data = fgetcsv($handle, 1000, "#")) !== false) 
    {
      $linha = str_replace('"','',$data[0]);
      $dados[] = explode(";", $linha);
    };
    fclose($arquivo);

    
    echo'<pre>';
    print_r($dados);
    echo'</pre>';
    

    
?>




