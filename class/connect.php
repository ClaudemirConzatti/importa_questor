<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//$servidor = 'vm-planner1';
$servidor = '177.37.95.152';
//$servidor = 'localhost';
$usuario2 = 'root';
$senha2 = 'sisinfo';

$banco = '';

$con_mysql = new mysqli($servidor, $usuario2, $senha2, $banco);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

?>
<?php
/*
  $connection_string = 'Driver=Firebird/InterBase(r) driver;Dbname=vm-planner1/3050:C:\nQuestorBase\Questor.FDB;CHARSET=NONE;PWD=pl@##$r2@16;UID=SYSDBA;Client=C:\xampp\apache\fbclient.dll;';
   $usuario1 = "SYSDBA"; 
   $senha1 = "pl@##$r2@16"; 
   $dbh=odbc_connect($connection_string,$usuario1, $senha1) or die ('nao foi possível conectar ao banco de dados - ' .$connection_string);
 */
  /*
  // $servidor = '192.168.0.126/3050:D:\web\nQuestor\NQUESTOR.FDB';
   $servidor = 'vm-planner1/3050:C:\nQuestorBase\Questor.FDB';
   $dbh=ibase_connect($servidor, 'SYSDBA', 'pl@##$r2@16"')or die('Erro ao conectar: '.ibase_errmsg());
*/
   
?>
