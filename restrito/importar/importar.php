<?php
if(!isset($_SESSION))
    session_start();
include('../../class/connect.php');

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

if(trim($_SESSION['base'])==''){
   echo "<script>location.href='../../index.php';</script>";
};

$base 		= $_SESSION['base'];
$escritorio = $_SESSION['escritorio'];
$DtI 		= $_SESSION['DtI'];
$DtF 		= $_SESSION['DtF'];

$i=0;

if(isset($_POST["doc"]))
{
foreach($_POST["doc"] as $doc)
    {
        $i++;
		if($i>1){$doc1.= "','" . $doc;}else{$doc1="'".$doc;};
    }
	$doc1.="'";
};
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

    <title>Cadastro</title>
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
                      	<fieldset>
                      		<div class="form-group">
                               <h6>
                            	<table class="table table-condensed table-striped table-responsive table-bordered">
                            		<thead>
                                    	<tr bgcolor="#D3D3D3" >
                                            <th width="10%" align="center">Status</th>
                                            <th width="90%" align="center">Comando</th> 
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
													, CASE
													    WHEN CONTARECEBER.CODIGOESCRIT IN(2) THEN 'CAIXA'
														ELSE 'CONTA'
													  END								AS  LANCAMENTO
													  
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
												AND NUMERODCTOCR in($doc1)

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
                                                    , CASE
                                                         WHEN CONTARECEBER.CODIGOESCRIT IN(2) THEN 'CAIXA'
                                                     	ELSE 'CONTA'
                                                       END																				
											";
												//$y= odbc_exec($dbh, $sql_questor); 
												$y= ibase_query ($dbh, $sql_questor);
												//while ($x = odbc_fetch_array($y)) 
												while ($x = ibase_fetch_object($y)){ 
												
												$cod_cli =$x->COD_CLIENTE; 
												$sql_cliente = "
												  SELECT Cod FROM $base.cli WHERE cod_questor = $cod_cli;
												"; 
												$y_cliente = mysqli_query($con_mysql,$sql_cliente) or die($sql_cliente);
												while($x_cliente = mysqli_fetch_array($y_cliente)){
													$fornecedor = $x_cliente['Cod'];
												}
												
												
												$total = $total+$x->VALOR;

												$nf			= $x->DOCUMENTO;
												$dpl		= $x->NOSSO_NUMERO;
												$Desp		= $x->COD_FINANCEIRO;
												$datacom	= $x->DT_LANCAMENTO;
												$datavenc	= $x->VENCIMENTO;
												$codca		= 100;
												$obs		= '' ;
												$Lanc		= $x->LANCAMENTO;
												$tipoi		= utf8_decode('CRÉDITO');
												$CodSerie	= 2;	
												$Doc		= 'Em Aberto';	
												$datavenc	= $x->VENCIMENTO;
												$valor		= $x->VALOR;
												$Imprimir	= 'Sim';
												
												$sql_insert = "
													INSERT INTO $base.fluxo (
														NF
														, Dpl
														, Fornecedor
														, Desp
														, Valor
														, DataCom
														, DataVenc
														, CodCa
														, Obs
														, Lanc
														, Tipo
														, CodSerie
														, Doc
														, DataVencBaixa
														, ValorVenc
														, Imprimir
														)
													VALUES (
														$nf
														, '$dpl'
														, $fornecedor
														, $Desp
														, $valor
														, '$datacom'
														, '$datavenc'
														, $codca
														, '$obs'
														, '$Lanc'
														, '$tipoi'
														, $CodSerie
														, '$Doc'
														, '$datavenc'
														, $valor
														, '$Imprimir'
														);			
												";
											   $sql_verifica = "
												  SELECT 
												  	* 
												  FROM $base.fluxo 
												  WHERE NF		= $nf	
												  AND CodSerie	= $CodSerie	
												  AND Fornecedor= $fornecedor
												  AND Desp		= $Desp

											   ";
												$y_verifica = mysqli_query($con_mysql,$sql_verifica);
												$num_rows = mysqli_num_rows($y_verifica);
												if($num_rows==0&&($codca!='NULL')&&($Desp!='NULL')){
													mysqli_query($con_mysql,$sql_insert)or die(printf("Errormessage: %s\n", mysqli_error($con_mysql)).'<p>'.utf8_encode($sql_insert));
													$importado='OK';
												}elseif(($codca=='NULL')&&($Desp=='NULL')){
													$importado='Título sem Conta ou Centro de Custo!!!!';
												}else{
													$importado='Já existe!!! '.$num_rows;
													
												};
												
												echo ('
												<tr>
													<td align="center" valign="middle">
													   '.$importado.'		
													</td>
													<td>'.utf8_encode($sql_insert).'</td>
												</tr>
												');
											};
												echo'
												<tr>
													<strong>
														<td align="">&nbsp;</td>
														<td align="right">'.number_format($total,2,',','.').'</td> 
													</strong>
												</tr>
												'
										?>
                                    </tbody>
                                </table>
                              </h6>
                      		</div>
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
