<?php

  include('../../class/connect.php');
  ini_set('display_errors',0);

  $empresa  = $_POST['empresa'];
  $CodPessoaFolha  = $_POST['CodPessoaFolha'];
  $CodPessoaFinanceiro  = $_POST['CodPessoaFinanceiro'];
  $importar  = $_POST['importar'];
  

  $sql = "SELECT * FROM eventos_rh.pessoa WHERE Empresa= $empresa AND CodPessoaFolha = $CodPessoaFolha";
  $y=mysqli_query($con_mysql,$sql);
  $rowcount=mysqli_num_rows($y);
  echo $sql.'<br>';
  if($rowcount==0){
    $sql = "INSERT INTO eventos_rh.pessoa (Empresa, CodPessoaFolha, CodPessoaFinanceiro,importar)VALUES ('$empresa',$CodPessoaFolha,$CodPessoaFinanceiro,'$importar')";
  }else{
    $sql = "UPDATE eventos_rh.pessoa SET CodPessoaFinanceiro = $CodPessoaFinanceiro, importar='$importar' WHERE Empresa=$empresa AND CodPessoaFolha=$CodPessoaFolha";
  }
  $y=mysqli_query($con_mysql,$sql);
  if (!$y) {
    $erro = 1;
}

echo $erro.$sql;

?>

