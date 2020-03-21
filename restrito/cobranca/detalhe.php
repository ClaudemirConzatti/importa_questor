<?php
	if(!isset($_SESSION))
	session_start();
	include('../../class/connect.php');
	ini_set('display_errors',0);

	if(trim($_SESSION['base'])==''){
	echo "<script>location.href='../../index.php';</script>";
	};
	$tipo = utf8_decode('Crédito'); 


	
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

    <title>Cobrança</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/sb-admin-2.css" rel="stylesheet">

    <script src="../../css/jquery.min.js"></script>
    <script src="../../css/bootstrap.min.js"></script>    
	<script src="js/jquery-3.1.1.min.js"></script>
  	<script src="js/bootstrap.min.js"></script>


</head>
<body >
    <div class="container">
        <div class="row">
            <div class="">
                <div class="panel-default panel panel-success">
                    <div class="panel-heading" align="center">
                        <h3 class="panel-danger">Cobrança Detalhe</h3>
                    </div>
                    <div class="panel-body">
						<fieldset>
							<form enctype="multipart/form-data" id="form1" name="form1" method="post" action="" >
								<div class="col-md-2">
									<input name="" id="" class="btn btn-danger" type="button" value="Fechar" onclick="sair();">
								</div>
							</form>
						</fieldset>

                      	<fieldset>
                      		<div class="form-group">
                               <h6>
                                    	<?php

											$sql_venc = "
												SELECT 
													Base
													, CASE
														WHEN Base = 'Financeiro_Planner' THEN 'Planner Solucoes Empresariais'
														WHEN Base = 'Financeiro_assessoria' THEN 'Planner Assessoria Empresarial'
														WHEN Base = 'financeiro_contabilidade' THEN 'Contabilidade Rio do Sul'
														WHEN Base = 'Financeiro_timbo' THEN 'Contabilidade Timbo'
														WHEN Base = 'Financeiro_Tecnologia' THEN 'Planner Tecnologia'
													END  				AS Empresa
													, Razao	
													, EmpCli			
													, Mun
													, Fone
													, Fax
													, Cont
													, Email
													, N					AS id_fluxo
													, NF
													, CodSerie
													, Dpl
													, Fornecedor
													, Valor
													, DataCom
													, DataVenc
													, ValorVenc
													, DataVencBaixa
													, DataPagto
													, ValorPagto
													, Obs
													, DataRetorno
													, ObsCob
													, Tipo
												FROM(
														/*************Financeiro_Planner*******************/
														SELECT 
															'Financeiro_Planner' AS Base
															, case 
																when ltrim(rtrim(Nome)) <>'' then cli.nome
																else cli.Razao
															 end as Razao
															 , cli.Razao as EmpCli  
															 , cli.Mun
															, cli.Fone
															, cli.Fax
															, cli.Cont
															, cli.Email
															, fluxo.N
															, fluxo.NF
															, fluxo.CodSerie
															, fluxo.Dpl
															, fluxo.Fornecedor
															, fluxo.Valor
															, fluxo.DataCom
															, fluxo.DataVenc
															, fluxo.ValorVenc
															, fluxo.DataVencBaixa
															, fluxo.DataPagto
															, fluxo.ValorPagto
															, fluxo.Obs
															, fluxo.DataRetorno
															, fluxo.ObsCob
															, fluxo.Tipo
														
														FROM Financeiro_Planner.fluxo 
														JOIN Financeiro_Planner.cli ON cli.Cod = fluxo.Fornecedor
														
														/*************Financeiro_assessoria*******************/
														UNION ALL
														SELECT 
															'Financeiro_assessoria' AS Base
															, case 
																when ltrim(rtrim(Nome)) <>'' then cli.nome
																else cli.Razao
															 end as Razao
															 , cli.Razao as EmpCli  
															 , cli.Mun
															, cli.Fone
															, cli.Fax
															, cli.Cont
															, cli.Email
															, fluxo.N
															, fluxo.NF
															, fluxo.CodSerie
															, fluxo.Dpl
															, fluxo.Fornecedor
															, fluxo.Valor
															, fluxo.DataCom
															, fluxo.DataVenc
															, fluxo.ValorVenc
															, fluxo.DataVencBaixa
															, fluxo.DataPagto
															, fluxo.ValorPagto
															, fluxo.Obs
															, fluxo.DataRetorno
															, fluxo.ObsCob
															, fluxo.Tipo
														
														FROM Financeiro_assessoria.fluxo 
														JOIN Financeiro_assessoria.cli ON cli.Cod = fluxo.Fornecedor
													
														
														/*************financeiro_contabilidade*******************/
														UNION ALL
														SELECT 
															'financeiro_contabilidade' AS Base
															, case 
																when ltrim(rtrim(Nome)) <>'' then cli.nome
																else cli.Razao
															 end as Razao
															 , cli.Razao as EmpCli  
															 , cli.Mun
															, cli.Fone
															, cli.Fax
															, cli.Cont
															, cli.Email
															, fluxo.N
															, fluxo.NF
															, fluxo.CodSerie
															, fluxo.Dpl
															, fluxo.Fornecedor
															, fluxo.Valor
															, fluxo.DataCom
															, fluxo.DataVenc
															, fluxo.ValorVenc
															, fluxo.DataVencBaixa
															, fluxo.DataPagto
															, fluxo.ValorPagto
															, fluxo.Obs
															, fluxo.DataRetorno
															, fluxo.ObsCob
															, fluxo.Tipo
														
														FROM financeiro_contabilidade.fluxo 
														JOIN financeiro_contabilidade.cli ON cli.Cod = fluxo.Fornecedor
														
														/*************Financeiro_timbo*******************/
														UNION ALL
														SELECT 
															'Financeiro_timbo' AS Base
															, case 
																when ltrim(rtrim(Nome)) <>'' then cli.nome
																else cli.Razao
															 end as Razao
															 , cli.Razao as EmpCli  
															 , cli.Mun
															, cli.Fone
															, cli.Fax
															, cli.Cont
															, cli.Email
															, fluxo.N
															, fluxo.NF
															, fluxo.CodSerie
															, fluxo.Dpl
															, fluxo.Fornecedor
															, fluxo.Valor
															, fluxo.DataCom
															, fluxo.DataVenc
															, fluxo.ValorVenc
															, fluxo.DataVencBaixa
															, fluxo.DataPagto
															, fluxo.ValorPagto
															, fluxo.Obs
															, fluxo.DataRetorno
															, fluxo.ObsCob
															, fluxo.Tipo
														
														FROM Financeiro_timbo.fluxo 
														JOIN Financeiro_timbo.cli ON cli.Cod = fluxo.Fornecedor
														
														/**************Financeiro_Tecnologia******************/
														UNION ALL
														SELECT 
															'Financeiro_Tecnologia' AS Base
															, case 
																when ltrim(rtrim(Nome)) <>'' then cli.nome
																else cli.Razao
															 end as Razao
															, cli.Razao as EmpCli  
															, cli.Mun
															, cli.Fone
															, cli.Fax
															, cli.Cont
															, cli.Email
															, fluxo.N
															, fluxo.NF
															, fluxo.CodSerie
															, fluxo.Dpl
															, fluxo.Fornecedor
															, fluxo.Valor
															, fluxo.DataCom
															, fluxo.DataVenc
															, fluxo.ValorVenc
															, fluxo.DataVencBaixa
															, fluxo.DataPagto
															, fluxo.ValorPagto
															, fluxo.Obs
															, fluxo.DataRetorno
															, fluxo.ObsCob
															, fluxo.Tipo
														
														FROM Financeiro_Tecnologia.fluxo 
														JOIN Financeiro_Tecnologia.cli ON cli.Cod = fluxo.Fornecedor
														
												)fluxo_unificado

												WHERE 1=1 
												AND DataVenc < CURRENT_DATE
												AND coalesce(DataRetorno,DataVenc) < CURRENT_DATE
												AND DataPagto is null
												AND Tipo = '$tipo'	
												$CLIENTE
												$base
												ORDER BY razao	
												
											";
											$y_venc = mysqli_query($con_mysql,$sql_venc);
											$cli='';
											echo'<div class="panel-group">';
											while($x = mysqli_fetch_array($y_venc)){
												if($cli!=$x['Razao']){
													$cli=$x['Razao'];
													echo utf8_encode('
														</div>
														<div class="panel panel-primary">
															<div class="panel-heading">
																<h4>
																'.$x['Razao'].' - 
																'.$x['Fone'].'  -
																'.$x['Cont'].'
																</h2>
															</div>
															<div class="panel-body">
																<b>	
																	<div class="col-md-2">Empresa</div>
																	<div class="col-md-1">NF</div>
																	<div class="col-md-1">Valor</div>
																	<div class="col-md-1">Vcto</div>
																	<div class="col-md-4">Obs</div>
																	<div class="col-md-3">Programado</div>
																</b>
															</div>
														
													');
												};
														echo utf8_encode('
															<div class="panel-body">	
																	<div class="col-md-2"><b>'.$x['Empresa'].'</b><i><br>'.$x['EmpCli'].'</i></div>
																	<div class="col-md-1">'.$x['NF'].'</div>
																	<div class="col-md-1">'.number_format($x['Valor'],2,',','.').'</div>
																	<div class="col-md-1">'.implode('/',array_reverse(explode('-',$x['DataVenc']))).'</div>
																	<div class="col-md-4">
																		<textarea name="ObsCob" id="ObsCob" rows="3" cols="30" class="form-control" >'.trim(nl2br($x['ObsCob'])).'</textarea>
																	</div>
																	<div class="col-md-3">
																		<input type="date" name="DataRetorno" id="DataRetorno" class="form-control" value="'.$x['DataRetorno'].'">
																	</div>
																	<input type="hidden" name="N" id="N" value="'.$x['id_fluxo'].'">
																	<input type="hidden" name="base" id="base" value="'.$x['Base'].'">
															</div>
												');
											};
											echo'</div>';
										?>
                              </h6>
                      		</div>
                      	</fieldset>
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
          <h4>Custo não atuallizado!!!</h4>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" id="btn_fecha_erro">Fechar</button>
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
   function atualiza(){
			location.reload(true);
   }

	$(document).ready(function() { // instancia a função após carregar a página
		$(document).on('blur', 'input#DataRetorno', function() { // faz a ação onblur no campo input id='valor'
			if ($(this).val()) {
				inserir_registo(this);  // executa a função inserir_registro
				atualiza(this); //executa a função calcCusto
			}
		});
	});
   
    function inserir_registo(objeto) {
        var base = $(objeto).parent().parent().find('input#base').val();
        var N = $(objeto).parent().parent().find('input#N').val();
        var DataRetorno = $(objeto).parent().parent().find('input#DataRetorno').val();
        var ObsCob = $(objeto).parent().parent().find('textarea#ObsCob').val();

        //dados a enviar, vai buscar os valores dos campos que queremos enviar para a BD
        var dadosajax = {
            'base' : base,
            'N'   : N,
            'DataRetorno' : DataRetorno,
            'ObsCob' : ObsCob
        };

        console.log(dadosajax); // mostra o resultado da função dadosajax
        $("#modal_aguarde").modal('show');

        pageurl = 'gravar.php';
        $.ajax({
            //url da pagina
            url: pageurl,
            //parametros a passar
            data: dadosajax,
            //tipo: POST ou GET
            type: 'POST',
            //cache
            cache: false,
            //se ocorrer um erro na chamada ajax, retorna este alerta
            //possiveis erros: pagina nao existe, erro de codigo na pagina, falha de comunicacao/internet, etc etc etc
            error: function(){
                $("#modal_erro").modal('show');
            },
            //retorna o resultado da pagina para onde enviamos os dados
            success: function(result)
            { 
                //se foi inserido com sucesso
                $('#modal_aguarde').modal('hide');
                if($.trim(result) == '1')
                {
									$('#modal_aguarde').modal('hide');
									location.reload(true);
									//alert("O seu registo foi inserido com sucesso!");
                }
                //se foi um erro
                else
                {	alert($.trim(result));
                    $('#modal_aguarde').modal('hide');
                    $("#modal_erro").modal('show');
                    //alert("Ocorreu um erro ao inserir o seu registo!");
                }

            }
        });
    }

    function gravar_() {
        pageurl = 'gravar.php?grava=S';
        $.ajax({
            url: pageurl,
            //tipo: POST ou GET
            type: 'GET',
            cache: false,
        });
    }
    function sair() {
        self.close();
    }
</script>
