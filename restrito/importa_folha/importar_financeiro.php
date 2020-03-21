<?php

  include('../../class/connect.php');
  ini_set('display_errors',0);

  $sql  = $_POST['sql'];

  foreach( $sql as $result){

    $Tipo		    = utf8_decode($result['Tipo']);
    $NF         = $result['NF'];
    $CodSerie   = 99;
    $Dpl        = $result['Dpl'];
    $Boleto     = utf8_decode('Não');
    $Imprimir   = 'Sim';
    $Lanc       = 'CONTA';
    $Doc        = 'Folha '.$result['Doc'];
    $Fornecedor = $result['Fornecedor'];
    $CodCa      = $result['CodCa'];
    $Desp       = $result['Desp'];
    $valor      = $result['valor'];
    $data_vence = $result['data_vence'];
    $data_vence = $result['data_vence'];
    $valor      = $result['valor'];
    $data_vence = $result['data_vence'];
	  $periodo	  = $result['periodo'];
    $base		    = $result['base'];
    
    $rowcount = 0;
    $sql_fluxo="select count(*) as qtd from $base.fluxo  WHERE Fornecedor = $Fornecedor AND NF='$NF' and  CodSerie = $CodSerie and Desp = $Desp and ValorVenc = $valor " ;
    $y=mysqli_query($con_mysql,$sql_fluxo);
    $CodPessoaFinanceiro=0;
    while($x=mysqli_fetch_array($y)){$rowcount =$x['qtd'];};

    if($rowcount<=0){
      $sql_insert="
        INSERT INTO $base.fluxo (
              Tipo
              , NF
              , CodSerie
              , Dpl
              , Boleto
              , Imprimir
              , Lanc
              , Doc
              , Fornecedor
              , CodCa
              , Desp
              , Valor
              , DataCom
              , DataVenc
              , ValorVenc
              , DataVencBaixa
              , Obs
        )VALUES (
              '$Tipo'
              , $NF
              , $CodSerie
              , '$Dpl'
              , '$Boleto'
              , '$Imprimir'
              , '$Lanc'
              , '$Doc'
              , $Fornecedor
              , $CodCa
              , $Desp
              , $valor
              , '$data_vence'
              , '$data_vence'
              , $valor
              , '$data_vence'
              , 'Folha Ref. $periodo'
        )" ;    
    }else{
      $sql_insert='';
    }
    $y=mysqli_query($con_mysql,$sql_insert);
    if (!$y) {
      $erro = mysqli_error($con_mysql);
      echo $erro.'<br>';
    }else{
      $erro = 1;
    }
  
  }
  echo $erro;
?>

