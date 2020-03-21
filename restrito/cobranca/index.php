<?php
	if(!isset($_SESSION))
	session_start();
	include('../../class/connect.php');
	ini_set('display_errors',0);

	if(trim($_SESSION['base'])==''){
	echo "<script>location.href='../../index.php';</script>";
	};
	$base 		= $_SESSION['base'];
	$tipo = utf8_decode('Crédito'); 

	$cli = $_POST['cli'];
	
	if(isset($_POST['situacao_atraso']))			{$situacao_atraso 			= "'".$_POST['situacao_atraso']."',";}else{$situacao_atraso='';};
	if(isset($_POST['situacao_reneg_vencer']))		{$situacao_reneg_vencer 	= "'".$_POST['situacao_reneg_vencer']."',";}else{$situacao_reneg_vencer='';};
	if(isset($_POST['situaca_reneg_atraso']))		{$situaca_reneg_atraso 		= "'".$_POST['situaca_reneg_atraso']."',";}else{$situaca_reneg_atraso='';};
	if(isset($_POST['situacao_vencer']))			{$situacao_vencer 			= "'".$_POST['situacao_vencer']."',";}else{$situacao_vencer='';};
	if(isset($_POST['situacao_cheque_vencer']))		{$situacao_cheque_vencer 	= "'".$_POST['situacao_cheque_vencer']."',";}else{$situacao_cheque_vencer='';};
	if(isset($_POST['situacao_cheque_devolvido']))	{$situacao_cheque_devolvido = "'".$_POST['situacao_cheque_devolvido']."',";}else{$situacao_cheque_devolvido='';};
	if(isset($_POST['situacao_cheque_nao_baixado'])){$situacao_cheque_nao_baixado= "'".$_POST['situacao_cheque_nao_baixado']."',";}else{$situacao_cheque_nao_baixado='';};
	if(isset($_POST['situacao_outros']))			{$situacao_outros 			= "'".$_POST['situacao_outros']."',";}else{$situacao_outros='';};


	$situacao = " AND situacao in (";
	$situacao.= $situacao_atraso;
	$situacao.= $situacao_reneg_vencer;
	$situacao.= $situaca_reneg_atraso;
	$situacao.= $situacao_vencer;
	$situacao.= $situacao_cheque_vencer;
	$situacao.= $situacao_cheque_devolvido;
	$situacao.= $situacao_cheque_nao_baixado;
	$situacao.= $situacao_outros;

	$situacao = substr($situacao,0,-1);	
	$situacao.=")";

	$check_marca_em_dia= $_POST['check_marca_em_dia'];
	$check_marca_atraso= $_POST['check_marca_atraso'];

	$dti = $_POST['dti'];
	$dtf = $_POST['dtf'];
	$data = date('Y-m-d');

	$_SESSION['cli'] = $cli;
	$_SESSION['sit'] = $sit;
	$_SESSION['dti'] = $dti;
	$_SESSION['dtf'] = $dtf;

	if(!isset($_SESSION['dti'])){
		$_SESSION['dti']='2000-01-01';
	};
	if(!isset($_SESSION['dtf'])){
		$_SESSION['dtf'] = date('Y-m-d');
	};

	$CLIENTE ='';
	if(isset($_SESSION['cli'])){
		$CLIENTE = " AND Razao LIKE '%$cli%'";
	};

	$tipo_data = $_POST['tipo_data'];
	if($tipo_data==1){
		$data_rel="
			AND DataRetorno between '$dti' and '$dtf'
		";
	}elseif($tipo_data==2){
		$data_rel="
			AND (CASE 
					WHEN LEFT(Situacao,11) = 'RENEGOCIADO'
						THEN VencimentoProrrogado
					ELSE DataVenc
				END) between '$dti' and '$dtf'
		";
	}elseif($tipo_data==3){
		$data_rel="
			AND (CASE 
					WHEN LEFT(Situacao,11) = 'RENEGOCIADO'
						THEN coalesce(DataRetorno,VencimentoProrrogado)
					ELSE coalesce(DataRetorno,DataVenc) 
				END) between '$dti' and '$dtf'
		";
	}elseif($tipo_data==4){
		$data_rel="
			AND (CASE 
					WHEN LEFT(Situacao,11) = 'RENEGOCIADO'
						THEN coalesce(DataContato,DataRetorno,VencimentoProrrogado)
					ELSE coalesce(DataContato,DataRetorno,DataVenc) 
				END) between '$dti' and '$dtf'
		";
	}

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
	<script src="js/printThis.js"></script>

</head>
<body >
    <div class="container ">
        <div class="row">
            <div class="">
                <div class="panel-default panel panel-primary">
                    <div class="panel-heading" align="center">
                        <h3 class="panel-danger">Cobrança</h3>
                    </div>
                    <div class="panel-body">
						<fieldset>
							<form enctype="multipart/form-data" id="form1" name="form1" method="post" action="" >
								<fieldset>
									<div class="col-md-4">	
										Cliente:
										<input class="form-control" type="text" name="cli" id="cli" value="<?php echo $_SESSION['cli'] ?>">
									</div>
									<div class="col-md-2">	
										Tipo Data:
										<select id="tipo_data" name="tipo_data" class="form-control" >
											<option value="1" <?php if($tipo_data==1){echo'selected';};?>>Pelo Retorno</option>
											<option value="2" <?php if($tipo_data==2){echo'selected';};?>>Pelo Vencimento</option>
											<option value="3" <?php if($tipo_data==3){echo'selected';};?>>Pelo Retorno/Vencimento</option>
											<option value="4" <?php if($tipo_data==4){echo'selected';};?>>Pelo Contato</option>
										</select>
									</div>
									<div class="col-md-2">	
										Data inicial:
										<input class="form-control" type="date" name="dti" id="dti" value="<?php echo $_SESSION['dti']?>">
									</div>
									<div class="col-md-2">	
										Data final:
										<input class="form-control" type="date" name="dtf" id="dtf" value="<?php echo $_SESSION['dtf'] ?>">
									</div>
									<div class="col-md-2">
										<br>
										<input type="submit" name="button" id="button" value="Consultar"  class="btn btn-info"/>
										<input name="" id="" class="btn btn-danger" type="button" value="Fechar" onclick="sair();">
									</div>
								</fieldset>
								<fieldset><hr>
									<div class="col-md-12">
										Situação:<br>
									</div>
								</fieldset>
								<fieldset>
									<b>
									<div class="col-md-3">
										<input name="check_marca_em_dia" id="check_marca_em_dia" type="checkbox" value="check_marca_em_dia" <?php if($check_marca_em_dia=='check_marca_em_dia'){echo 'checked';}; ?> onclick="marca_em_dia()">A VENCER
									</div>
									<div class="col-md-3">
										<input name="check_marca_atraso" id="check_marca_atraso" type="checkbox" value="check_marca_atraso" <?php if($check_marca_atraso=='check_marca_atraso'){echo 'checked';}; ?> onclick="marca_atraso()">EM ATRASO
									</div>
									</b>
								</fieldset><hr>
								<fieldset>
									<div class="col-md-3">
										<input name="situacao_atraso" id="situacao_atraso" type="checkbox" value="EM ATRASO" <?php if($situacao_atraso=="'EM ATRASO',"){echo 'checked';} ?>>EM ATRASO
									</div>
									<div class="col-md-3">
										<input name="situacao_reneg_vencer" id="situacao_reneg_vencer" type="checkbox" value="RENEGOCIADO A VENCER" <?php if($situacao_reneg_vencer=="'RENEGOCIADO A VENCER',"){echo 'checked';} ?>>RENEGOCIADO A VENCER
									</div>
									<div class="col-md-3">
										<input name="situaca_reneg_atraso" id="situaca_reneg_atraso" type="checkbox" value="RENEGOCIADO EM ATRASO" <?php if($situaca_reneg_atraso=="'RENEGOCIADO EM ATRASO',"){echo 'checked';} ?>>RENEGOCIADO EM ATRASO
									</div>
									<div class="col-md-3">
										<input name="situacao_vencer" id="situacao_vencer" type="checkbox" value="A VENCER" <?php if($situacao_vencer=="'A VENCER',"){echo 'checked';} ?>>A VENCER
									</div>
									<div class="col-md-3">
										<input name="situacao_cheque_vencer" id="situacao_cheque_vencer" type="checkbox" value="CHEQUE A VENCER" <?php if($situacao_cheque_vencer=="'CHEQUE A VENCER',"){echo 'checked';} ?>>CHEQUE A VENCER
									</div>
									<div class="col-md-3">
										<input name="situacao_cheque_devolvido" id="situacao_cheque_devolvido" type="checkbox" value="CHEQUE DEVOLVIDO" <?php if($situacao_cheque_devolvido=="'CHEQUE DEVOLVIDO',"){echo 'checked';} ?>>CHEQUE DEVOLVIDO
									</div>
									<div class="col-md-3">
										<input name="situacao_cheque_nao_baixado" id="situacao_cheque_nao_baixado" type="checkbox" value="CHEQUE NÃO BAIXADO" <?php if($situacao_cheque_nao_baixado=="'CHEQUE NÃO BAIXADO',"){echo 'checked';} ?>>CHEQUE NÃO BAIXADO
									</div>
									<div class="col-md-3">
										<input name="situacao_outros" id="situacao_outros" type="checkbox" value="OUTROS" <?php if($situacao_outros=="'OUTROS',"){echo 'checked';} ?>>OUTROS
									</div>
								</fieldset><hr>
							</form>
						</fieldset>

                      	<fieldset>
                      		<div class="form-group">
                               <h6>
                                    	<?php

											$sql_venc = "
											SELECT 
												CASE
													WHEN Base = 'Financeiro_Planner' THEN 'Planner Solucoes Empresariais'
													WHEN Base = 'Financeiro_assessoria' THEN 'Planner Assessoria Empresarial'
													WHEN Base = 'financeiro_contabilidade' THEN 'Contabilidade Rio do Sul'
													WHEN Base = 'Financeiro_timbo' THEN 'Contabilidade Timbo'
													WHEN Base = 'Financeiro_Tecnologia' THEN 'Planner Tecnologia'
												END  				AS Empresa
												, Base
												, Razao	
												, count(N)			AS qtd_reg
												, EmpCli			
												, Mun
												, Fone
												, Fax
												, Cont
												, Situacao
												, sum(Valor)		AS Valor
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
															, CASE
																WHEN (left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') )
																	THEN NULL
																ELSE fluxo.DataPagto
															  END	AS DataPagto
															, fluxo.ValorPagto
															, fluxo.Obs
															, fluxo.DataRetorno
															, fluxo.ObsCob
															, fluxo.Tipo
															,CASE 
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh IS NULL OR VenctoCh < '1900-01-01'))
																	THEN 'EM ATRASO'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01'))
																	THEN 'RENEGOCIADO A VENCER'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01'))
																	THEN 'RENEGOCIADO EM ATRASO'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc >= CURRENT_date)
																	THEN 'A VENCER'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01')
																	THEN 'CHEQUE A VENCER'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') 
																	THEN 'CHEQUE DEVOLVIDO'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL  
																	THEN 'CHEQUE NÃO BAIXADO'
																ELSE 'OUTROS'
															END AS Situacao
															,DATEDIFF(CURDATE(), DATE(fluxo.DataVenc)) AS DiasAberto
															,CASE
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>360 THEN 'ACIMA DE 360 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>120 THEN 'ACIMA DE 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>90 THEN 'DE 91 A 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>60 THEN 'DE 61 A 90 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>30 THEN 'DE 31 A 60 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>0 THEN 'ATE 30 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-360 THEN 'ACIMA DE 360 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-120 THEN 'ACIMA DE 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-90 THEN 'DE 91 A 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-60 THEN 'DE 61 A 90 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-30 THEN 'DE 31 A 60 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<=0 THEN 'ATE 30 DIAS'
																ELSE 'OUTRO' 
															END AS PeriodoVencimento	
															,VenctoCh AS VencimentoProrrogado
														
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
															, CASE
																WHEN (left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') )
																	THEN NULL
																ELSE fluxo.DataPagto
															  END	AS DataPagto
															, fluxo.ValorPagto
															, fluxo.Obs
															, fluxo.DataRetorno
															, fluxo.ObsCob
															, fluxo.Tipo
															,CASE 
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh IS NULL OR VenctoCh < '1900-01-01'))
																	THEN 'EM ATRASO'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01'))
																	THEN 'RENEGOCIADO A VENCER'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01'))
																	THEN 'RENEGOCIADO EM ATRASO'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc >= CURRENT_date)
																	THEN 'A VENCER'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01')
																	THEN 'CHEQUE A VENCER'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') 
																	THEN 'CHEQUE DEVOLVIDO'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL  
																	THEN 'CHEQUE NÃO BAIXADO'
																ELSE 'OUTROS'
															END	AS Situacao
															,DATEDIFF(CURDATE(), DATE(fluxo.DataVenc)) AS DiasAberto
															,CASE
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>360 THEN 'ACIMA DE 360 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>120 THEN 'ACIMA DE 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>90 THEN 'DE 91 A 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>60 THEN 'DE 61 A 90 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>30 THEN 'DE 31 A 60 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>0 THEN 'ATE 30 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-360 THEN 'ACIMA DE 360 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-120 THEN 'ACIMA DE 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-90 THEN 'DE 91 A 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-60 THEN 'DE 61 A 90 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-30 THEN 'DE 31 A 60 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<=0 THEN 'ATE 30 DIAS'
																ELSE 'OUTRO' 
															END AS PeriodoVencimento	
															,VenctoCh AS VencimentoProrrogado														
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
															, CASE
																WHEN (left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') )
																	THEN NULL
																ELSE fluxo.DataPagto
															  END	AS DataPagto
															, fluxo.ValorPagto
															, fluxo.Obs
															, fluxo.DataRetorno
															, fluxo.ObsCob
															, fluxo.Tipo
															,CASE 
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh IS NULL OR VenctoCh < '1900-01-01'))
																	THEN 'EM ATRASO'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01'))
																	THEN 'RENEGOCIADO A VENCER'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01'))
																	THEN 'RENEGOCIADO EM ATRASO'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc >= CURRENT_date)
																	THEN 'A VENCER'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01')
																	THEN 'CHEQUE A VENCER'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') 
																	THEN 'CHEQUE DEVOLVIDO'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL  
																	THEN 'CHEQUE NÃO BAIXADO'
																ELSE 'OUTROS'
															END AS Situacao
															,DATEDIFF(CURDATE(), DATE(fluxo.DataVenc)) AS DiasAberto
															,CASE
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>360 THEN 'ACIMA DE 360 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>120 THEN 'ACIMA DE 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>90 THEN 'DE 91 A 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>60 THEN 'DE 61 A 90 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>30 THEN 'DE 31 A 60 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>0 THEN 'ATE 30 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-360 THEN 'ACIMA DE 360 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-120 THEN 'ACIMA DE 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-90 THEN 'DE 91 A 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-60 THEN 'DE 61 A 90 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-30 THEN 'DE 31 A 60 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<=0 THEN 'ATE 30 DIAS'
																ELSE 'OUTRO' 
															END AS PeriodoVencimento	
															,VenctoCh AS VencimentoProrrogado
															
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
															, CASE
																WHEN (left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') )
																	THEN NULL
																ELSE fluxo.DataPagto
															  END	AS DataPagto
															, fluxo.ValorPagto
															, fluxo.Obs
															, fluxo.DataRetorno
															, fluxo.ObsCob
															, fluxo.Tipo
															,CASE 
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh IS NULL OR VenctoCh < '1900-01-01'))
																	THEN 'EM ATRASO'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01'))
																	THEN 'RENEGOCIADO A VENCER'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01'))
																	THEN 'RENEGOCIADO EM ATRASO'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc >= CURRENT_date)
																	THEN 'A VENCER'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01')
																	THEN 'CHEQUE A VENCER'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') 
																	THEN 'CHEQUE DEVOLVIDO'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL  
																	THEN 'CHEQUE NÃO BAIXADO'
																ELSE 'OUTROS'
															END AS Situacao
															,DATEDIFF(CURDATE(), DATE(fluxo.DataVenc)) AS DiasAberto
															,CASE
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>360 THEN 'ACIMA DE 360 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>120 THEN 'ACIMA DE 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>90 THEN 'DE 91 A 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>60 THEN 'DE 61 A 90 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>30 THEN 'DE 31 A 60 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>0 THEN 'ATE 30 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-360 THEN 'ACIMA DE 360 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-120 THEN 'ACIMA DE 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-90 THEN 'DE 91 A 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-60 THEN 'DE 61 A 90 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-30 THEN 'DE 31 A 60 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<=0 THEN 'ATE 30 DIAS'
																ELSE 'OUTRO' 
															END AS PeriodoVencimento	
															,VenctoCh AS VencimentoProrrogado	
															
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
															, CASE
																WHEN (left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') )
																	THEN NULL
																ELSE fluxo.DataPagto
															  END	AS DataPagto
															, fluxo.ValorPagto
															, fluxo.Obs
															, fluxo.DataRetorno
															, fluxo.ObsCob
															, fluxo.Tipo
															,CASE 
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh IS NULL OR VenctoCh < '1900-01-01'))
																	THEN 'EM ATRASO'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01'))
																	THEN 'RENEGOCIADO A VENCER'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01'))
																	THEN 'RENEGOCIADO EM ATRASO'
																WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc >= CURRENT_date)
																	THEN 'A VENCER'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01')
																	THEN 'CHEQUE A VENCER'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') 
																	THEN 'CHEQUE DEVOLVIDO'
																WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND fluxo.CodBanco IS NOT NULL AND fluxo.BaixaCh IS NULL  
																	THEN 'CHEQUE NÃO BAIXADO'
																ELSE 'OUTROS'
															END AS Situacao
															,DATEDIFF(CURDATE(), DATE(fluxo.DataVenc)) AS DiasAberto
															,CASE
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>360 THEN 'ACIMA DE 360 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>120 THEN 'ACIMA DE 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>90 THEN 'DE 91 A 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>60 THEN 'DE 61 A 90 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>30 THEN 'DE 31 A 60 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))>0 THEN 'ATE 30 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-360 THEN 'ACIMA DE 360 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-120 THEN 'ACIMA DE 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-90 THEN 'DE 91 A 120 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-60 THEN 'DE 61 A 90 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<-30 THEN 'DE 31 A 60 DIAS'
																WHEN DATEDIFF(CURDATE(), DATE(fluxo.DataVenc))<=0 THEN 'ATE 30 DIAS'
																ELSE 'OUTRO' 
															END AS PeriodoVencimento	
															,VenctoCh AS VencimentoProrrogado
															
														FROM Financeiro_Tecnologia.fluxo 
														JOIN Financeiro_Tecnologia.cli ON cli.Cod = fluxo.Fornecedor
														
												)fluxo_unificado

												WHERE 1=1 
												AND DataPagto is null
												$data_rel
												AND Tipo = '$tipo'	
												$CLIENTE
												$situacao
												GROUP BY
													Base
													, Razao	
													, EmpCli			
													, Mun
													, Fone
													, Fax
													, Cont
													, Situacao
												ORDER BY razao	
											";
											/*
											echo '<pre>';
												print_r($sql_venc);
											echo '</pre>';
											*/
											$y_venc = mysqli_query($con_mysql,$sql_venc);
											$cli='';
											$valor_total=0;
											echo'<div class="panel-group">';
											while($x = mysqli_fetch_array($y_venc)){
												$base = $x['Base'];	
												$valor_total= $valor_total+$x['Valor'];
												if($cli!=$x['Razao']){
													$cli=$x['Razao'];

													$link = '<a href="javascript:abrir';
													$link.="('aponta.php?cli=$cli','1200','700')";
													$link.='"><span class="glyphicon glyphicon-earphone"></span> Aponta</a>';
	
													$link='<button class="btn btn-default" onclick="';
													$link.='javascript:abrir';
													$link.="('aponta.php?cli=$cli','1200','700')";
													$link.='"><i class="glyphicon glyphicon-earphone"></i></button>';

													echo utf8_encode('
														</div>
														<div class="panel panel-primary">
															<div class="panel-heading">
																<h4>
																<button type="button" title="Inserir Contato" class="btn btn-primary" 
																		data-toggle="modal" data-target="#ModalInsercao" data-whatever_cliente="'.$cli.'"
																		data-toggle="modal" data-target="#ModalInsercao" data-whatever_data="'.$data.'"
																		data-toggle="modal" data-target="#ModalInsercao" data-whatever_data_fin="'.$dtf.'"
																		data-toggle="modal" data-target="#ModalInsercao" data-whatever_altera="N"
																		
																>
																	<i class="glyphicon glyphicon-earphone"></i>
																</button>

																<button type="button" title="Consultar Dpl"	class="btn btn-primary view_data" 
																	data-toggle="modal" data-target="#ModalConsulta" data-whatever_cliente2="'.$cli.'"
																	data-toggle="modal" data-target="#ModalConsulta" data-whatever_data_sit2="'.$situacao.'"
																	data-toggle="modal" data-target="#ModalConsulta" data-whatever_data_tipo_data="'.$tipo_data.'"
																	data-toggle="modal" data-target="#ModalConsulta" data-whatever_data_ini2="'.$dti.'"
																	data-toggle="modal" data-target="#ModalConsulta" data-whatever_data_fin2="'.$dtf.'"
																>
																	<i class="glyphicon glyphicon-list-alt"></i>
																</button>

																<button type="button" title="Historico Contato" class="btn btn-primary view_data_cobranca 
																	data-toggle="modal" data-target="#ModalCobranca" data-whatever_cliente1="'.$cli.'"
																	data-toggle="modal" data-target="#ModalCobranca" data-whatever_data_ini="'.$dti.'"
																	data-toggle="modal" data-target="#ModalCobranca" data-whatever_data_fin="'.$dtf.'"
																	
																>
																	<i class="glyphicon glyphicon-header"></i>
																</button>
																
																'.$x['Razao'].' - 
																'.$x['Fone'].'  -
																'.$x['Cont'].'
																
																</h2>
															</div>
															<div class="panel-body">
																<b>	
																	<div class="col-md-3">Empresa</div>
																	<div class="col-md-3">Cliente</div>
																	<div class="col-md-3">Status</div>
																	<div class="col-md-2">Qtd Reg.</div>
																	<div class="col-md-1">Valor</div>
																</b>
															</div>
														
													');
												};
												$link = '<a href="javascript:abrir';
												$link.="('detalhe.php?cli=$cli&base=$base','1200','700')";
												$link.='"><i>'.$x['EmpCli'].'</i></a>';
														echo utf8_encode('
															<div class="panel-body">	
																	<div class="col-md-3">
																		<b>'.$x['Empresa'].'</b>
																	</div>
																	<div class="col-md-3">
																		<i>'.$x['EmpCli'].'</i>
																	</div>
																	<div class="col-md-3">
																		<b>'.$x['Situacao'].'</b>
																	</div>
																	<div class="col-md-2">'.number_format($x['qtd_reg'],0,',','.').'</div>
																	<div class="col-md-1">'.number_format($x['Valor'],2,',','.').'</div>
															</div>
												');
											};
											echo'</div>';
											echo utf8_encode('
											<div class="panel panel-info">
											<div class="panel-heading">											
												<div class="panel-body">
													<h4>	
														<div class="col-md-9">
															<b>Total</b>
														</div>
														<div class="col-md-3" align="right">R$ '.number_format($valor_total,2,',','.').'</div>
													</h4>
												</div>
											</div>
											</div>');
										?>
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
<!-- ************************ Modal inclusão de comentários *************************************** -->
<div class="modal fade" id="ModalInsercao" tabindex="-1" role="dialog" aria-labelledby="ModalInsercaoLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="ModalInsercaoLabel">Contato com o Clinete:</h4>
				</div>
			</div>
		</div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Data para Retorno:</label>
            <input type="date" class="form-control" id="DataRetorno" name="DataRetorno" >
            <input type="hidden" class="form-control" id="Cliente" name="Cliente" >
            <input type="hidden" class="form-control" id="DataContato" name="DataContato" >
            <input type="hidden" class="form-control" id="data_fin" name="data_fin" >
            <input type="hidden" class="form-control" id="Altera" name="Altera" >
            <input type="hidden" class="form-control" id="Id" name="Id" >
          </div>
          <div class="form-group">
            <label for="message-text" class="control-label">Obs:</label>
            <textarea class="form-control" id="ObsCob" name="ObsCob"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" name="grava" onclick="inserir_registo(this);" id="grava" data-dismiss="modal">Gravar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- *********************** modal de erro e aguarde!!!! ******************************************** -->
<div class="modal fade" id="modal_sucesso">
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

<!--  script para carregar informações para o modal de inserção das obs     -->
<script>
	$('#ModalInsercao').on('show.bs.modal', function (event) {
		
		var button = $(event.relatedTarget) // Button that triggered the modal
		var Cliente = button.data('whatever_cliente') 
		var DataRetorno = button.data('whatever_data') 
		var data_fin = button.data('whatever_data_fin') 
		var Altera = button.data('whatever_altera') 
		var Id = button.data('whatever_id') 
		var obs = button.data('whatever_obs') 
		
		var modal = $(this)
		modal.find('.modal-title').text(Cliente)
		modal.find('input#Cliente').val(Cliente)
		modal.find('input#DataRetorno').val(DataRetorno)
		modal.find('input#DataContato').val(DataRetorno)
		modal.find('input#data_fin').val(data_fin)
		modal.find('input#Altera').val(Altera)
		modal.find('textarea#ObsCob').val(obs)
		modal.find('input#Id').val(Id)
		
		})
</script>

<!--*********************Ajax para inserir registro****************************** -->
<script>
    function inserir_registo(objeto) {
        var Cliente = $(objeto).parent().parent().find('input#Cliente').val();
        var DataRetorno = $(objeto).parent().parent().find('input#DataRetorno').val();
        var DataContato = $(objeto).parent().parent().find('input#DataContato').val();
        var data_fin = $(objeto).parent().parent().find('input#data_fin').val();
        var ObsCob = $(objeto).parent().parent().find('textarea#ObsCob').val();
        var Altera = $(objeto).parent().parent().find('input#Altera').val();
        var Id = $(objeto).parent().parent().find('input#Id').val();

        //dados a enviar, vai buscar os valores dos campos que queremos enviar para a BD
        var dadosajax = {
          'Cliente' : Cliente,
          'DataRetorno' : DataRetorno,
					'DataContato' : DataContato,
					'data_fin' : data_fin,	
					'Altera' : Altera,	
					'Id' : Id,	
					'ObsCob' : ObsCob
        };

        console.log(dadosajax); // mostra o resultado da função dadosajax
        $("#modal_sucesso").modal('show');

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
                $('#modal_sucesso').modal('hide');
                if($.trim(result) == 1)
                {
					location.reload(true);
					$('#modal_sucesso').modal('hide');
                }
                //se foi um erro
                else
                {	alert($.trim(result));
                    $('#modal_sucesso').modal('hide');
                    $("#modal_erro").modal('show');
                    //alert("Ocorreu um erro ao inserir o seu registo!");
                }

            }
        });
    }
</script>

<!--*********************Consulta registro****************************** -->
<div class="modal" tabindex="-1" id="ModalConsulta"  role="dialog" aria-labelledby="ModalConsultaLabel" >
	<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<i id="print">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<div class="modal-header">
								<h4 class="modal-title" id="ModalConsultaLabel"></h4>
							</div>
						</div>
					</div>
					<div class="modal-body">
							<span id="visualusuario"></span>  
					</div>
				</i>
				<div class="modal-footer">
					<a id="advanced" href="#nada" class="button button-primary">
						<button type="button" class="btn btn-warning" id="advanced">Imprimir</button>
					</a>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
				</div>

			</div>
	</div>
</div>

<script>
		$(document).ready(function(){
			$('#ModalConsulta').on('show.bs.modal', function (event) {
						var button = $(event.relatedTarget) // Button that triggered the modal
						var modal = $(this)
						var data_ini = button.data('whatever_data_ini2') 
						var data_fin = button.data('whatever_data_fin2') 
						var cliente = button.data('whatever_cliente2') 
						var situacao = button.data('whatever_data_sit2') 
						var tipo_data = button.data('whatever_data_tipo_data') 
						
						modal.find('.modal-title').text(cliente)
						
				if(cliente!=''){
					var dados = {
						cliente:cliente,
						situacao:situacao,
						tipo_data:tipo_data,
						data_fin:data_fin,
						data_ini:data_ini

					};
					
					$.post('consulta.php', dados, function(retorna){
						$('#visualusuario').html(retorna);
					});
					
				};
			});
		});
</script>


<!--*********************Consulta Cobranca****************************** -->
<div class="modal fade" id="ModalCobranca" tabindex="-1" role="dialog" aria-labelledby="ModalCobrancaLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
		<div class="panel panel-primary">
			<div class="panel-heading">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="ModalCobrancaLabel"></h4>
			  </div>
			</div>
		</div>
      <div class="modal-body">
				<span id="visualcobranca"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Sair</button>
      </div>
    </div>
  </div>
</div>
<script>
		$(document).ready(function(){
			$('#ModalCobranca').on('show.bs.modal', function (event) {
						var button = $(event.relatedTarget) // Button that triggered the modal
						var modal = $(this)
						var data_ini = button.data('whatever_data_ini') 
						var data_fin = button.data('whatever_data_fin') 
						var cliente = button.data('whatever_cliente1') 
						modal.find('.modal-title').text(cliente)
						
				if(cliente!=''){
					var dados = {
						cliente:cliente,
						data_fin:data_fin,
						data_ini:data_ini

					};
					
					$.post('consulta_cobranca.php', dados, function(retorna){
						$('#visualcobranca').html(retorna);
						//$('#ModalCobranca').modal('show');
					});
					
				};
			});
		});
</script>


<script>
	function sair(){
		window.close();
	}
	
	function marca_todos(){
		if($('input#check_todos').is(":checked")){
			$('input#situacao_atraso').prop("checked", true);
			$('input#situacao_reneg_vencer').prop("checked", true);
			$('input#situaca_reneg_atraso').prop("checked", true);
			$('input#situacao_vencer').prop("checked", true);
			$('input#situacao_cheque_vencer').prop("checked", true);
			$('input#situacao_cheque_devolvido').prop("checked", true);
			$('input#situacao_cheque_nao_baixado').prop("checked", true);
			$('input#situacao_outros').prop("checked", true);
			$('input#check_marca_em_dia').prop("checked", true);
			$('input#check_marca_atraso').prop("checked", true);
		}else{
			$('input#situacao_atraso').prop("checked", false);
			$('input#situacao_reneg_vencer').prop("checked", false);
			$('input#situaca_reneg_atraso').prop("checked", false);
			$('input#situacao_vencer').prop("checked", false);
			$('input#situacao_cheque_vencer').prop("checked", false);
			$('input#situacao_cheque_devolvido').prop("checked", false);
			$('input#situacao_cheque_nao_baixado').prop("checked", false);
			$('input#situacao_outros').prop("checked", false);
			$('input#check_marca_em_dia').prop("checked", false);
			$('input#check_marca_atraso').prop("checked", false);
		}
	}

	function marca_em_dia(){
		if($('input#check_marca_em_dia').is(":checked")){
			$('input#situacao_reneg_vencer').prop("checked", true);
			$('input#situacao_vencer').prop("checked", true);
			$('input#situacao_cheque_vencer').prop("checked", true);
		}else{
			$('input#situacao_reneg_vencer').prop("checked", false);
			$('input#situacao_vencer').prop("checked", false);
			$('input#situacao_cheque_vencer').prop("checked", false);
		}
	}
	function marca_atraso(){
		if($('input#check_marca_atraso').is(":checked")){
			$('input#situacao_atraso').prop("checked", true);
			$('input#situaca_reneg_atraso').prop("checked", true);
			$('input#situacao_cheque_devolvido').prop("checked", true);
			$('input#situacao_cheque_nao_baixado').prop("checked", true);
			$('input#situacao_outros').prop("checked", true);
		}else{
			$('input#situacao_atraso').prop("checked", false);
			$('input#situaca_reneg_atraso').prop("checked", false);
			$('input#situacao_cheque_devolvido').prop("checked", false);
			$('input#situacao_cheque_nao_baixado').prop("checked", false);
			$('input#situacao_outros').prop("checked", false);
		}
	}


</script>

<script>
    $('#advanced').on("click", function () {
      $('#print').printThis({
        importCSS: true,
		loadCSS: "../../css/bootstrap.min.css",
		loadCSS: "../../css/sb-admin-2.css"
      });
    });
</script>