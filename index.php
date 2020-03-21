<?php
if(!isset($_SESSION))
    session_start();

//Login de Usários
if(isset($_POST['login'])){
  $_SESSION['base'] = ($_POST['base']);
  echo "<script>location.href='restrito/index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="imagens/icone.png"> 

    <title>Login</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<style>
.container{
    width: 100%;
    height: 100%;
    position: absolute;
	background-image: url("imagens/inicial.png");
	opacity: 0.7;
    filter: alpha(opacity=70); /* For IE8 and earlier */
	background-size: cover;
}
</style>
</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading" align="center">
                        <h3 class="panel-title">Base</h3>
                    </div>
                    <div class="panel-body">
                      <form method="post" action="" role="form">
                        <fieldset>
                          <fieldset>
                            <div class="form-group">
                              <select name="base" class="form-control" >
                                  <?php  
                                    $arquivo = fopen ('class/bases_questor.txt', 'r');
                                    while(!feof($arquivo))
                                    {
                                        $linha = explode('->',fgets($arquivo));
                                         echo utf8_encode('
										<option value="'.$linha[1].'">'.$linha[0].'</option>	
										');
                                    };
                                    fclose($arquivo);
                                  ?>
                               </select>
                             </div>
                          </fieldset>  
                            </fieldset>  
                                <button type="submit" name="login" value="true" class="btn btn-success btn-block">
                                  <span class="glyphicon  glyphicon-log-in"></span>  Selecione
                                </button>
                            </fieldset>
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>

</html>