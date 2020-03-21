<?php
if(!isset($_SESSION))
    session_start();
    $base 	= $_SESSION['base'];
	//$email 	= $_SESSION['email'];
	if(trim($_SESSION['base'])==''){
	   echo "<script>location.href='../index.php';</script>";
	};

	date_default_timezone_set('America/Sao_Paulo');
	
	$arquivo = fopen ('../class/bases_questor.txt', 'r');
	while(!feof($arquivo))
	{
		$linha = explode('->',fgets($arquivo));
		if($linha[1]==$base){
			$escritorio = $linha[2];
			$_SESSION['escritorio']=$escritorio;
		}
	};
	fclose($arquivo);
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Grupo Planner</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script  src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script  src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="shortcut icon" href="../imagens/icone.png">   
</head>
<style>
.container {
    width: 100%;
    height: 100%;
    position: absolute;
	background: url(../imagens/panel.png) no-repeat;
	opacity: 0.2;
    filter: alpha(opacity=20); /* For IE8 and earlier */
	background-size: cover;
}
.row{
    width: 100%;
    height: 100%;
    position: absolute;
	z-index:100;
	margin-left:10;
	margin-right:-20;
	margin-top:0;
}

</style>

<body>
<div class="container"></div>
<div class="row">
    <nav class="navbar navbar-inverse">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Grupo Planner</a>
        </div>
        <ul class="nav navbar-nav">
         
          <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Cadastros <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li>
                    <a href="../index.php"><span class="glyphicon glyphicon-log-in"></span> Login</a>
                </li>
            </ul>
          </li>
          <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Movimentos<span class="caret"></span></a>
            <ul class="dropdown-menu">
            <li>
                <a href="importar/index.php" target="_blank">
            		<span class="glyphicon glyphicon-calendar"></span> Importar
                </a>
            </li>
            <li>
                <a href="importa_cnab/localiza_arq.php" target="_blank">
            		<span class="glyphicon glyphicon-calendar"></span> Importar CNAB(400)
                </a>
            </li>
            <li>
                <a href="cobranca/index.php" target="_blank">
            		<span class="glyphicon glyphicon-calendar"></span> Cobrança
                </a>
            </li>
            <li>
                <a href="importa_folha/localiza_arq.php" target="_blank">
            		<span class="glyphicon glyphicon-calendar"></span> Folha
                </a>
            </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
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
<script language="JavaScript">
            function abrir(URL,w,h) {
                var width = w;
                var height = h;
				var left = (screen.width-width)/2;
				var top = (screen.height-height)/2;
                var newwindow =window.open(URL,'janela', 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=no, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=no, fullscreen=no');
				//posiciona o popup no centro da tela
				newwindow.moveTo(left, top);
				newwindow.focus();
				return false;
				
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



