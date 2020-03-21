<?php
if(!isset($_SESSION))
session_start();
include('../../class/connect.php');

ini_set('display_errors',0);

if(trim($_SESSION['base'])==''){
echo "<script>location.href='../../index.php';</script>";
};

$base 		= $_SESSION['base'];
$escritorio = $_SESSION['escritorio'];
$dt = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
	<link rel="shortcut icon" href="../../imagens/icone.png">
  <link href="../../css/bootstrap.min.css" rel="stylesheet">
  <link href="../../css/sb-admin-2.css" rel="stylesheet">
  <title>Importa</title>
</head>

<body>
<div class="container">
        <div class="row">
            <div class="col-md-5 col-md-offset-4">
                <div class="panel-default panel panel-success">
                    <div class="panel-heading" align="center">
                        <h3 class="panel-danger">Localiza Arquivo (Ret)</h3>
                    </div>
                    <div class="panel-body">
                      <form enctype="multipart/form-data" id="form1" name="form1" method="post" action="importa.php" target="_blank">
                        <table class="table-condensed">
                            <tr>
                              <td>
                                Arquivo:
                                <input type="file" class="form-control" name="file_csv" id="file_csv" accept=".ret,.txt"  />
                              </td>
                            </tr>
                            <tr>
                              <td>
                                Data Tarifa:
                                <input type="date" class="form-control" name="dtTarifa" id="dtTarifa"  value="<?php echo $dt ?>"/>
                              </td>
                            </tr>
                            <tr>
                              <td align="center">
                                  <input type="submit" name="button" id="button" value="Importar"  class="btn btn-info"/>
                                  <input name="" id="" class="btn btn-danger" type="button" value="Fechar" onclick="sair();">
                              </td>
                            </tr>
                          </table>
                        </form>
                      </div>
                </div>
            </div>
        </div>
        <div class="row">
       	<div class="container-fluid">
            <div class="navbar-fixed-bottom">
               <div class="col-md-4"><strong>Base:</strong><?php echo $base ?></div>
               <div class="col-md-4"><strong>Escritório:</strong><?php echo  $_SESSION['escritorio']?></div>
               <div class="col-md-4"><strong><div id="horas" ></div></strong></div>
            </div>
    	</div>    
    </div>

    </div>
</body>
</html>

<script>
    function sair(){
      window.close();
    }


    function relogio(){
	var data = new Date();
	var horas = data.getHours();
	var minutos = data.getMinutes();
	var segundos = data.getSeconds();
	var exibe = document.getElementById("horas");
	var dia     = data.getDate();           // 1-31
	var dia_sem = data.getDay();            // 0-6 (zero=domingo)
	var mes     = data.getMonth();          // 0-11 (zero=janeiro)
	var ano2    = data.getYear();           // 2 dígitos
	var ano4    = data.getFullYear();       // 4 dígitos

	switch (new Date().getDay()) {
		case 0:
			day = "Domingo";
			break;
		case 1:
			day = "Segunda-Feira";
			break;
		case 2:
			day = "Terça-Feira";
			break;
		case 3:
			day = "Quarta-Feira";
			break;
		case 4:
			day = "Quinta-Feira";
			break;
		case 5:
			day = "Sexta-Feira";
			break;
		case 6:
			day = "Sábado";
	}


	switch (new Date().getMonth()) {
		case 0:
			mes_ext = "Janeiro";
			break;
		case 1:
			mes_ext = "Fevereiro";
			break;
		case 2:
			mes_ext = "Março";
			break;
		case 3:
			mes_ext = "Abril";
			break;
		case 4:
			mes_ext = "Maio";
			break;
		case 5:
			mes_ext = "Junho";
			break;
		case 6:
			mes_ext = "Julho";
			break;
		case 7:
			mes_ext = "Agosto";
			break;
		case 8:
			mes_ext = "Setembro";
			break;
		case 9:
			mes_ext = "Outubro";
			break;
		case 10:
			mes_ext = "Novembro";
			break;
		case 11:
			mes_ext = "Dezembro";
			break;
	}

	
	exibe.innerHTML =day+" - "+dia+" de  "+mes_ext+" de "+ano4+" ("+ horas + ":" + minutos + ":" + segundos+")";
}
setInterval(relogio, 1000);
</script>

</script>