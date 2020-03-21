<?php
if(!isset($_SESSION))
    session_start();
include('../../class/connect.php');

ini_set('display_errors',1);

if(trim($_SESSION['base'])==''){
   echo "<script>location.href='../../index.php';</script>";
};

$base 		= $_SESSION['base'];
$escritorio = $_SESSION['escritorio'];
$dtTarifa	= $_REQUEST['dtTarifa'];
$banco    = $_POST['banco'];

	
    $sql_banco = "
        SELECT 
          CodbancoCnab	    AS BancoCodigo
          , CodAgencia 		  AS CodigoAgencia 
          , CodContaCnab	  AS CodigoCedente
          , CodContaDigito 	AS DigitoCodigoCedente
        FROM $base.bancos 
        WHERE N =  $banco
        LIMIT 1    
    ";
    //echo $sql_banco.'<br>';
    $y_banco   = mysqli_query($con_mysql,$sql_banco);
    while($x_banco = mysqli_fetch_array($y_banco)){
        $conta = $x_banco['CodigoCedente'];
        $conta = str_pad($conta , 8 , '0' , STR_PAD_LEFT);
        $Banco_financeiro_confere =  intval(str_pad($x_banco['BancoCodigo'], 3 , '0' , STR_PAD_LEFT));
		    $Ag_Financeiro =  '';
		    $Conta_Financeiro =  intval(str_pad($x_banco['CodigoCedente'], 7 , '0' , STR_PAD_LEFT));
        $BANCO_FINANCEIRO = $x_banco['BancoCodigo'];
        $ContaConfereFin = $Banco_financeiro_confere.$Ag_Financeiro.$Conta_Financeiro;
        
    };
	
	$Array_tarifas = array();
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
  <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
  <link rel="stylesheet" href="js/bootstrap.min.css">
  <script src="js/jquery-3.1.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>

  <title>Importa</title>

    <style>
        .container {
        margin: auto;
        width: 100% !important;
        }
        .table-condensed{
        font-size: 12px;
        display: inline-block;
        vertical-align: middle;
        }
        .table > tbody > tr > td {
            vertical-align: middle;
        }
        .form-control{
            font-size: 12px;
            text-align: right;
        }
        .esquerda{
            text-align: left;
        }
    </style>

</head>

<body>
<div class="container">
    <div class="row">
        <div class="">
            <div class="panel-default panel panel-success">
                <div class="panel-heading" align="center">
                    <h3 class="panel-danger">Retorno Bradesco</h3>
                </div>
                <div class="panel-body">
                <?php
                    $tot_titulo =0;
                    $tot_desconto=0;
                    $tot_abatimento=0;
                    $tot_juros=0;
                    $tot_tarifa=0;
                    $tot_vlr_pago=0;
                    
                    if (isset($_FILES)) {
                        $arquivoUpload = $_FILES['file_csv'];
                        move_uploaded_file($arquivoUpload['tmp_name'],'uploads/'.$arquivoUpload['name']);
						
                        $txt = 'uploads/'.$arquivoUpload['name'];
                        $arquivo = fopen ($txt, 'r');
                        
                        echo '<table class="table table-bordered table-condensed table-hover">';
                        $i=0;
                        while(!feof($arquivo)){
                          $linha = fgets($arquivo, 1024);
                          if(!$linha){
                            continue;
                          }
                          $identificador = substr($linha,0,1);
                          if($identificador==0){
                            $operacao   = substr($linha,2,7);
                            $banco      = substr($linha,76,3);
                            $seq_reg    = substr($linha,100,7);
                            $agencia    = '';
                            $conta      = intval(substr($linha,26,20));

                            $ContaConfere = $banco.$agencia.$conta;
                            echo $ContaConfereFin.'!='.$ContaConfere;
                            if($ContaConfereFin!=$ContaConfere){
                              $continua='N';
                              break;
                            };
                            
    
                          };
                          if($identificador==1 && $operacao=='RETORNO'){
                              $nota = substr($linha,116,10);
                              $nota = preg_replace("/[^0-9]/", "", $nota);
                              
                              $nosso_numero = intval(substr($linha,69,12));

                              $ocorrencia = substr($linha,108,02);
								
/*************************************buscando ocorrencia************************************************************/								
                              $sql_desp_ocorrencia = "
                                    SELECT 
                                      CodBanco
                                      , Ocorrencia
                                      , Descricao
                                      , CodConta
                                      , CodCa 
                                      , CodFor
                                      , MesmaDataBaixa
                                    FROM $base.ocorrencia 
                                    WHERE CodBanco = '$banco' 
                                    AND  Ocorrencia = '$ocorrencia'
                              ";
                              $y_desp_ocorrencia   = mysqli_query($con_mysql,$sql_desp_ocorrencia);
                      
                              while($x_desp_ocorrencia = mysqli_fetch_array($y_desp_ocorrencia)){
                                $Desc_ocorrencia 	= utf8_encode($x_desp_ocorrencia['Descricao']);
                                $CodDesp			= $x_desp_ocorrencia['CodConta'];
                                $CodCa				= $x_desp_ocorrencia['CodCa'];
                                $CodFor				= $x_desp_ocorrencia['CodFor'];
                                $MesmaDataBaixa		= $x_desp_ocorrencia['MesmaDataBaixa'];
                              };
/**********************************************************************************************************************/								
                                $dt_liquidacao = substr($linha,110,2).'/'.substr($linha,112,2).'/'.substr($linha,114,2);
                                $dt_liq = substr($linha,114,2).'-'.substr($linha,112,2).'-'.substr($linha,110,2);
                                $dt_credito = substr($linha,299,2).'-'.substr($linha,297,2).'-'.substr($linha,295,2);
                                
                                $vlr_titulo = substr($linha,152,11).'.'. substr($linha,163,2);
                                $vlr_titulo =floatval($vlr_titulo );
                                
                                $vlr_tarifa = substr($linha,181,5).'.'.substr($linha,186,2);
                                $vlr_tarifa =floatval($vlr_tarifa );
								
/****************************************populando array das tarifas*****************************************************/
								$Array_tarifas[]=array('CodDesp'=>$CodDesp,'vlr_tarifa'=>$vlr_tarifa);
								
/**********************************************************************************************************************/								
								
                                $outras_desp = substr($linha,188,11).'.'.substr($linha,200,2);
                                $outras_desp = floatval($outras_desp );

                                $abatimento = substr($linha,188,11).'.'.substr($linha,200,2);
                                $abatimento = floatval($abatimento );

                                $desconto = substr($linha,240,11).'.'.substr($linha,252,2);
                                $desconto = floatval($desconto );

                                $juros = substr($linha,266,11).'.'.substr($linha,278,2);
                                $juros = floatval($juros );

                                $vlr_recebido = substr($linha,253,11).'.'.substr($linha,264,2);
                                $vlr_recebido = floatval($vlr_recebido );
                                
                                $sql_upd="";
                                $ret =  "N";
                                $bgcolor_baixa='bgcolor="#FF6347"';
                                if($ocorrencia=='06' || $ocorrencia=='15'){ 
                                    $tot_desconto   =$tot_desconto + $desconto;
                                    $tot_titulo     =$tot_titulo  + $vlr_titulo;   
                                    $tot_abatimento =$tot_abatimento + $abatimento;
                                    $tot_juros      =$tot_juros  + $juros;    
                                    $tot_tarifa     =$tot_tarifa + $vlr_tarifa;   
                                    $tot_vlr_pago   =$tot_vlr_pago  + $vlr_recebido; 
									$sql_upd1='';
									$perc1='';
									$sql_dpl = ("
											SELECT 
												N
												, Valor
												, (SELECT sum(flx.Valor) FROM $base.fluxo AS flx WHERE flx.Dpl = fluxo.Dpl AND flx.NF = fluxo.NF ) AS Total
												
											FROM $base.fluxo 
											WHERE Dpl = '$nosso_numero'
											AND NF = $nota
											AND ValorPagto IS NULL							
										");
									//echo $sql_dpl.'<br>';	
									$y_dpl   = mysqli_query($con_mysql,$sql_dpl)or die(mysqli_error($con_mysql));
									while($x_dpl = mysqli_fetch_array($y_dpl)){
										$N = $x_dpl['N'];
										$valor = $x_dpl['Valor'];
										$total = $x_dpl['Total'];
										$perc = $valor/$total;
										$perc1.= $perc.'<p>';
										$vlr_recebido_bx = $vlr_recebido*$perc;
										
										$sql_upd="
											UPDATE $base.fluxo
											SET DataPagto 		    = '$dt_liq'
												,DataVencBaixa	    = '$dt_liq'
												, DataConc			= '$dt_credito'
												, ValorPagto 		= $vlr_recebido_bx
												, ValorVenc  		= $vlr_recebido_bx
												, CodBancoMov 	    = $BANCO_FINANCEIRO
												, Doc				= '$seq_reg'
											WHERE N = $N
											";
										$sql_upd1.=$sql_upd.'<p>';	
										mysqli_query($con_mysql,$sql_upd);
										if(mysqli_affected_rows($con_mysql) > 0){
											$ret =  "S";
											$bgcolor_baixa='bgcolor="#98FB98"';
										  }else{
											$ret =  "N";
											$bgcolor_baixa='bgcolor="#FF6347"';
										  }// fim do $sql_upd  
									}//	fim do sql_dpl									  
                                };

                                $i++;
                                if($i==1){    
                                    echo'
                                        <tr bgcolor="#c3c3c3">
                                            <th>Nota</th>
                                            <th>Nosso Número</th>
                                            <th>Ocorrência</th>
                                            <th>Dt Liqu.</th>
                                            <th>R$ Título</th>
                                            <th>Dt Créd.</th>
                                            <th>R$ Tarifa</th>
                                            <th>R$ Desp.</th>
                                            <th>R$ Abat.</th>
                                            <th>R$ Desc.</th>
                                            <th>R$ Juros</th>
                                            <th>R$ Recebido</th>
                                        </tr>
                                        <tbody>
                                    ';
                                };
                                echo'
                                    <tr>
                                        <td '.$bgcolor_baixa.'>'.$ret.' '.$nota.'</td>
                                        <td>'.$nosso_numero.'</td>
                                        <td align="center">'.$ocorrencia.'-'.$Desc_ocorrencia.'</td>
                                        <td align="center">'.implode('/',array_reverse(explode('-',$dt_liq))).'</td>
                                        <td align="right">'.number_format($vlr_titulo,2,',','.').'</td>
                                        <td align="center">'.implode('/',array_reverse(explode('-',$dt_credito))).'</td>
                                        <td align="right">'.number_format($vlr_tarifa,2,',','.').'</td>
                                        <td align="right">'.number_format($outras_desp,2,',','.').'</td>
                                        <td align="right">'.number_format($abatimento,2,',','.').'</td>
                                        <td align="right">'.number_format($desconto,2,',','.').'</td>
                                        <td align="right">'.number_format($juros,2,',','.').'</td>
                                        <td align="right">'.number_format($vlr_recebido,2,',','.').'</td>
                                        <td align="right">'.$perc1.'</td>
                                    </tr>
                                ';
                                
                            }
                        }
						if($continua!='N'){
								echo'
								</tbody>
								<tfoot>
								<tr bgcolor="#f3f3f3">
									<td colspan="4">Totais</td>
									<td align="right">'.number_format($tot_titulo,2,',','.').'</td>
									<td align="center"></td>
									<td align="right">'.number_format($tot_tarifa,2,',','.').'</td>
									<td align="right"></td>
									<td align="right">'.number_format($tot_abatimento,2,',','.').'</td>
									<td align="right">'.number_format($tot_desconto,2,',','.').'</td>
									<td align="right">'.number_format($tot_juros,2,',','.').'</td>
									<td align="right">'.number_format($tot_vlr_pago,2,',','.').'</td>
								</tr>
								</tfoot>
							';
					/***********************agrupando valores das tarifas por despesa***********************/						
								$Desp = array();
								$tarifa = array();
								foreach($Array_tarifas as $key => $value) {
									$Desp[$value['CodDesp']][] = $value['vlr_tarifa'];
									foreach($Desp as $keyP => $valueP) {
										$tarifa[$keyP] = array_sum($Desp[$keyP]);
									}
								}
								
								foreach($tarifa as $tar => $xtarifa) {
									  $codigo_despesa = $tar;	
									  $nf = intval($seq_reg.$codigo_despesa);
									  $valor_tarifa = $xtarifa*-1;
									  $obs          ="Tarifa retorno Bancario nr.: $seq_reg";
									  $tipoi        = utf8_decode("DÉBITO");
									  $CodSerie     = 1;
									  $Imprimir     = 'Sim';
									  $dt_credito	= $dtTarifa;

                  /*******************************************************************************************/	
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
													, DestinoCh
													, Lanc
													, Tipo
													, CodSerie
													, Doc
													, DataVencBaixa
													, ValorVenc
													, CodBancoMov
													, Imprimir
													, DataPagto
													, DataConc
													, ValorPagto
												)VALUES(
													$nf
													, '$nf'
													, $CodFor
													, $codigo_despesa
													, $valor_tarifa
													, '$dt_credito'
													, '$dt_credito'
													, $CodCa
													, '$obs'
													, '$obs'
													, 'CONTA'
													, '$tipoi'
													, $CodSerie
													, '$seq_reg'
													, '$dt_credito'
													, $valor_tarifa
													, $BANCO_FINANCEIRO
													, '$Imprimir'
													, '$dt_credito'
													, '$dt_credito'
													, $valor_tarifa
												)";
													$sql_verifica = "
															SELECT * FROM $base.fluxo WHERE NF =$nf and Fornecedor = $CodFor;
													";
													//echo $sql_verifica.'<br>';
														$y_verifica = mysqli_query($con_mysql,$sql_verifica);
														$num_rows = mysqli_num_rows($y_verifica);
													if($num_rows==0){
														mysqli_query($con_mysql,$sql_insert)or die(printf($importado=utf8_decode('Já Baixado ')).'<p>'.($sql_insert));
                            //echo $sql_insert.'<br>';
                          };		
									
								/*******************************************************************************************/	
								  
								};
					/***************************************************************************************/						

						};
                    echo '</table>';
						

                        fclose($arquivo);
						if($continua=='N'){	
							echo'
								<script>
									$(document).ready(function() {
										$("#modal_erro").modal("show");
									});
								</script>
							';
						}
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>    

  <div class="modal fade" id="modal_aguarde">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="alert alert-success" align="center">Aguarde!!!!</h2>
        </div>
        <div class="modal-body" align="center">
          <h4>Salvando!!!</h4>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal_erro">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="alert alert-danger" align="center">Atenção!!!!</h2>
        </div>
        <div class="modal-body" align="center">
          <h4>Conta Não Confere</h4>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" id="btn_fecha_erro">Fechar</button>
        </div>        
      </div>
    </div>
  </div>
  </body>
</html>

