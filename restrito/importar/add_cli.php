<?php
if(!isset($_SESSION))
    session_start();
include('../../class/connect.php');
set_time_limit(0);
if(trim($_SESSION['base'])==''){
   echo "<script>location.href='../../index.php';</script>";
};

$base 		= $_SESSION['base'];
$escritorio = $_SESSION['escritorio'];

$cod_questor	= $_REQUEST['cod_questor'];
$cod_financeiro	= $_REQUEST['cod_financeiro'];	
if(trim($cod_financeiro)!=''){
	$sql_upd = "
			UPDATE $base.cli
				SET cod_questor = $cod_questor
			WHERE Cod = $cod_financeiro;	
			";
	mysqli_query($con_mysql,$sql_upd) or die($sql_upd); 

	echo'
	  <script>
	     self.close();
		 window.opener.location.reload();
	  </script>
	';

};

?>
<link rel="shortcut icon" href="../../imagens/icone.png"> 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Importar</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/sb-admin-2.css" rel="stylesheet">
    <script src="../../css/jquery.min.js"></script>
    <script src="../../css/bootstrap.min.js"></script>    
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->


</head>
<body >

    <div class="container">
        <div class="row">
            <div class="">
                <div class="panel-default panel panel-success">
                    <div class="panel-heading" align="center">
                        <h3 class="panel-danger">Importar</h3>
                    </div>
                    <div class="panel-body">
                        <form id="form1" name="form1" method="post" action="" role="form">
                            <fieldset> 
                                <div class="form-group col-md-2">
                                	Cod Financeiro:	
                                      <input name="cod_financeiro" type="text"  class="form-control" id="cod_financeiro"  value="<?php echo $cod_financeiro ?>"autofocus>
                                </div>
                                <div class="form-group col-md-2">
                                    Cod.	Questor:
                                      <input name="cod_questor" type="text"  class="form-control" id="cod_questor"  value="<?php echo $cod_questor ?>" >
                                </div>
                                <div class="form-group col-md-2">
                                    &nbsp;
                              		<button type="submit" name="login" value="true" class="btn btn-success btn-block"  >
                                    	<span class="glyphicon glyphicon-floppy-saved"></span> Gravar
                                    </button>
                                </div>
                              <div class="form-group col-md-2">
                                    &nbsp;
                              		<button type="button" name="sair" value="" class="btn btn-danger btn-block" onClick="fechar()">
                                      <span class="glyphicon glyphicon-log-out"></span>	Sair
                                    </button>
                                </div>
                            </fieldset>    
                      	</form>
                      	<fieldset>
                        <form id="form2" name="form2" method="post" action="importar.php" role="form" target="_blank">
                      		<div class="form-group">
                               <h6>&nbsp;</h6>
                      		</div>
                        </form>
                      	</fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>

</html>
<script>
	function fechar(){
	  self.close();    
   }
</script>


