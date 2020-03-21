<?php
if(!isset($_SESSION))
    session_start();
include('../../class/connect.php');
set_time_limit(0);
/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/
if(trim($_SESSION['base'])==''){
   echo "<script>location.href='../../index.php';</script>";
};

$base 		= $_SESSION['base'];
$escritorio = $_SESSION['escritorio'];

$_SESSION['DtI']=$_POST['DtI'];
$_SESSION['DtF']=$_POST['DtF'];
$DtI 		= $_SESSION['DtI'];
$DtF 		= $_SESSION['DtF'];

if(trim($_SESSION['DtF'])==''){
    //$data = date( "Y-m-d", strtotime( "-1 month" ) );
    $ano = date( "Y");
    $mes = date( "m");
	$DtF = $ano.'-'.$mes.'-'.cal_days_in_month(CAL_GREGORIAN, $mes , $ano);
	$_SESSION['DtF']=$DtF;
}
if(trim($_SESSION['DtI'])==''){
    //$data = date( "Y-m-d", strtotime( "-1 month" ) );
    $ano = date( "Y");
    $mes = date( "m");
	$DtI = $ano.'-'.$mes.'-01';
	$_SESSION['DtI']=$DtI;

}

$total		= 0;

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
            <div class="col-md-offset-0">
                <div class="panel-default panel panel-success">
                    <div class="panel-heading" align="center">
                        <h3 class="panel-danger">Importar</h3>
                    </div>
                    <div class="panel-body">
                        <form id="form1" name="form1" method="post" action="" role="form">
                            <fieldset> 
                                <div class="form-group col-md-2">
                                    Data Inicial:
                                      <input name="DtI" type="date"  class="form-control" id="DtI"  value="<?php echo $DtI  ?>" autofocus>
                                </div>
                                <div class="form-group col-md-2">
                                    Data Final:
                                      <input name="DtF" type="date"  class="form-control" id="DtF"  value="<?php echo $DtF ?>">
                                </div>
                                <div class="form-group col-md-2">
                                    &nbsp;
                              		<button type="button" name="login" value="true" class="btn btn-success btn-block" onClick="Consultar();" >
                                    	<span class="glyphicon glyphicon-search"></span> Consultar
                                    </button>
                                </div>
                                <div class="form-group col-md-2">
                                    &nbsp;
                                    <button type="button" class="btn btn-info btn-block" onClick="Importar();" >
                                         <span class="glyphicon glyphicon-import"></span> Importar
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
                               <h6>
                            	<table class="table table-condensed table-responsive table-bordered">
                            		<thead>
                                    	<tr bgcolor="#D3D3D3" >
                                            <th width="6%" align="center">
                                              <input id="checkbox_master" onclick="marca_desmarca();" type="checkbox" class="form-control" ><span id="texto">Marcar</span></input>
                                            </th>
                                            <th width="4%" align="center">NF</th> 
                                            <th width="14%" align="center">CodSerie</th> 
                                            <th width="7%" align="center">Dpl</th> 
                                            <th width="11%" align="center">Cliente</th> 
                                            <th width="8%" align="center">NossoNr.</th> 
                                            <th width="11%" align="center">Cod.Desp</th> 
                                            <th width="14%" align="center">DataCom</th> 
                                            <th width="7%" align="center">DataVenc</th>
                                            <th width="4%" align="center">Valor</th>
                                            <th width="5%" align="center">Ques.</th>
                                            <th width="9%" align="center">Fin.</th> 
                                        </tr>
                                    </thead>
                                    <tbody >
                                    	<?php
										    $sql_questor = "
												SELECT 
													CONTARECEBER.CODIGOESCRIT			AS COD_ESCRITORIO
													, EMPRESA.NOMEEMPRESA				AS ESCRITORIO
													, CONTARECEBER.SERIENS				AS SERIE
													, CONTARECEBER.NUMERONS	  			AS DOCUMENTO			
													, CONTARECEBER.DATAEMISSAOCR 		AS EMISSAO		
													, CONTARECEBER.DATAVCTOCR			AS VENCIMENTO
													, SUM(VALORTOTALSERVNOTAITEM)		AS VALOR
													, CONTARECEBER.STATUSCR				AS STATUS
													, CONTARECEBER.NOSSONUMERO			AS NOSSO_NUMERO
													, CONTARECEBER.DATAHORALCTO			AS DT_LANCAMENTO
													, PESSOAFINANCEIRO.NOME				AS CLIENTE
													, CONTARECEBER.CODIGOCLIENTE		AS COD_CLIENTE
													, SERVICOESCRIT.UNIDSERVICOESCRIT   AS COD_FINANCEIRO
													, NUMERODCTOCR
												FROM CONTARECEBER 
												LEFT JOIN PESSOAFINANCEIRO ON PESSOAFINANCEIRO.CODIGOPESSOAFIN = CONTARECEBER.CODIGOCLIENTE
												LEFT JOIN ESCRITORIO ON ESCRITORIO.CODIGOESCRIT = CONTARECEBER.CODIGOESCRIT
												LEFT JOIN EMPRESA ON EMPRESA.CODIGOEMPRESA = ESCRITORIO.CODIGOEMPRESA
												
												LEFT JOIN SERVICONOTA ON SERVICONOTA.CODIGOESCRIT 	= CONTARECEBER.CODIGOESCRIT
																	 AND SERVICONOTA.SERIENS		= CONTARECEBER.SERIENS
																	 AND SERVICONOTA.NUMERONS		= CONTARECEBER.NUMERONS
																	 AND SERVICONOTA.CODIGOCLIENTE	= CONTARECEBER.CODIGOCLIENTE
												
												LEFT JOIN SERVICONOTAITEM ON SERVICONOTAITEM.CODIGOESCRIT	=SERVICONOTA.CODIGOESCRIT
																		 AND SERVICONOTAITEM.SERIENS		=SERVICONOTA.SERIENS
																		 AND SERVICONOTAITEM.NUMERONS		=SERVICONOTA.NUMERONS
												
												LEFT JOIN SERVICOESCRIT ON SERVICOESCRIT.CODIGOSERVICOESCRIT = SERVICONOTAITEM.CODIGOSERVICOESCRIT
												
												WHERE CONTARECEBER.CODIGOESCRIT IN ($escritorio)
												AND DATAEMISSAOCR BETWEEN '$DtI' AND '$DtF'
											
												GROUP BY
													CONTARECEBER.CODIGOESCRIT		
												    , EMPRESA.NOMEEMPRESA			
												    , CONTARECEBER.SERIENS			
		                                            , CONTARECEBER.NUMERONS	  		
                                                    , CONTARECEBER.DATAEMISSAOCR 	
                                                    , CONTARECEBER.DATAVCTOCR		
                                                    , CONTARECEBER.STATUSCR			
                                                    , CONTARECEBER.NOSSONUMERO		
                                                    , CONTARECEBER.DATAHORALCTO		
                                                    , PESSOAFINANCEIRO.NOME			
                                                    , CONTARECEBER.CODIGOCLIENTE	
                                                    , SERVICOESCRIT.UNIDSERVICOESCRIT  
                                                    , NUMERODCTOCR
											";
												//$y= odbc_exec($dbh, $sql_questor) or die ($sql_questor); 
												$y= ibase_query ($dbh, $sql_questor);
												//while ($x = odbc_fetch_array($y)) 
												while ($x = ibase_fetch_object($y)){ 
												$total = $total+$x->VALOR;
												$cod_cli = $x->COD_CLIENTE;
												$cod_cli_fin = 0;
												$link ="javascript:abrir('add_cli.php?cod_questor=$cod_cli','1100','400')";
/*********************************verificando se ja existe o vinculo do cliente no mysql***************************/
												$sql_cliente = "
												  SELECT Cod,coalesce(cod_questor,0) as cod_questor  FROM $base.cli WHERE cod_questor = $cod_cli;
												"; 
												//echo $x->DOCUMENTO.' - '.$sql_cliente.'<p>';
												$y_cliente = mysqli_query($con_mysql,$sql_cliente) or die($sql_cliente);
												while($x_cliente = mysqli_fetch_array($y_cliente)){
													$cod_cli_fin = $x_cliente['cod_questor'];
													$cod_financeiro = $x_cliente['Cod'];
												}
												  $btn_add_cli = '
													<button type="button" name="add_cli" value="true" class="btn btn-success btn-block" onClick="'.$link.'" >
														<span class="glyphicon glyphicon-new-window"></span> Cli
													</button>
												  '	;
/*******************************************************************************************************************/												
												$cod_desp = intval($x->COD_FINANCEIRO);
												
												if($cod_desp<=0 or $cod_cli_fin<=0){
													$bgcolor = 'bgcolor="#FF0000"';
													$cod_desp = ($x->COD_FINANCEIRO);
												}else{
													$bgcolor='';
												}
												
												echo utf8_encode('
												<tr '.$bgcolor.'>
													<td align="center" valign="middle">
													   <fieldset>
													   <div class="form-group col-md-2">
													   		<input type="checkbox" name="doc[]" value="'.$x->NUMERODCTOCR.'" id="doc[]" >
													   </div>
													   </fieldset>
													   <fieldset>
													   <div class="form-group col-md-12">
													   		'.$btn_add_cli.'
														</div>
														</fieldset>
													</td>
													<td >'.$x->COD_ESCRITORIO.'-'.$x->ESCRITORIO.'</td> 
													<td align="center">'.$x->SERIE.'</td> 
													<td align="center">'.$x->DOCUMENTO.'</td> 
													<td >'.($x->CLIENTE).'</td> 
													<td align="right">'.($x->NOSSO_NUMERO).'</td> 
													<td align="center">'.$cod_desp.'</td> 
													<td align="center">'.implode('/',array_reverse(explode('-',$x->EMISSAO))).'</td> 
													<td align="center">'.implode('/',array_reverse(explode('-',$x->VENCIMENTO))).'</td> 
													<td align="right">'.number_format($x->VALOR,2,',','.').'</td> 
													<td align="right">'.($cod_cli_fin).'</td> 
													<td align="right">'.($cod_financeiro).'</td> 
												</tr>
												');
											};
												echo'
												<tr>
													<strong>
														<td align="" colspan="9">Total</td>
														<td align="right">'.number_format($total,2,',','.').'</td> 
													</strong>
												</tr>
												'
										?>
                                    </tbody>
                                </table>
                              </h6>
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
<script>
   function Consultar(){
	    document.getElementById("form1").submit();  
   }
</script>
<script>
   function Importar(){
	    document.getElementById("form2").submit();  
   }
</script>


<script type="text/javascript">
function marca_desmarca()
{
var e = document.getElementsByTagName("input");
var x = document.getElementById("texto");
var master = document.getElementById("doc");	
var bool;

if (x.innerHTML == "Marcar") // if (master.checked == true) // <-- substituir "IF" para var master.checked sempre true.
{ bool = true; 	x.innerHTML = "Desmarcar"; 	}
else
{ bool = false; x.innerHTML = "Marcar";    	}

for(var i=1;i<e.length;i++)
{
	if (e[i].type=="checkbox")
	{
		e[i].checked = bool;
	}	
}
master.checked = false; // se var master.checked for sempre true -> apagar esta linha
}
</script>


<script language="JavaScript">
            function abrir(URL,w,h) {
                var width = w;
                var height = h;
				var left = (screen.width-width)/2;
				var top = (screen.height-height)/2;
                var newwindow =window.open(URL,'_blank', 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=no, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=no, fullscreen=no');
				//posiciona o popup no centro da tela
				newwindow.moveTo(left, top);
				newwindow.focus();
				return false;
				
            }
</script>
