<style>
    table, th, td {
        font-size: 12px;
        font-family: "Calibri";
    }
</style>
<?php
	include('../../class/connect.php');
    $total=0;
	$total_multa =0;
	$total_juros =0;
	$total_atualizado =0;
	
    $Razao          = trim(utf8_decode($_REQUEST['cliente']));
    $sit            = trim(utf8_decode($_REQUEST['situacao']));
    $tipo           = utf8_decode('Crédito');
    $data_ini       = $_REQUEST['data_ini'];
    $data_fin       = $_REQUEST['data_fin'];
    
    $tipo_data = $_POST['tipo_data'];
	if($tipo_data==1){
		$data_rel="
			AND DataRetorno between '$data_ini' and '$data_fin'
		";
	}elseif($tipo_data==2){
		$data_rel="
			AND (CASE 
					WHEN LEFT(Situacao,11) = 'RENEGOCIADO'
						THEN VenctoCh
					ELSE DataVenc
				END) between '$data_ini' and '$data_fin'
		";
	}elseif($tipo_data==3){
		$data_rel="
			AND (CASE 
					WHEN LEFT(Situacao,11) = 'RENEGOCIADO'
						THEN coalesce(DataRetorno,VenctoCh)
					ELSE coalesce(DataRetorno,DataVenc) 
				END) between '$data_ini' and '$data_fin'
		";
	}
    try
    {
        //consulta
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
			, Situacao
			,DATEDIFF(CURDATE(), DATE(DataVenc)) AS DiasAberto
			,CASE
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))>360 THEN 'ACIMA DE 360 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))>120 THEN 'ACIMA DE 120 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))>90 THEN 'DE 91 A 120 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))>60 THEN 'DE 61 A 90 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))>30 THEN 'DE 31 A 60 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))>0 THEN 'ATE 30 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))<-360 THEN 'ACIMA DE 360 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))<-120 THEN 'ACIMA DE 120 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))<-90 THEN 'DE 91 A 120 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))<-60 THEN 'DE 61 A 90 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))<-30 THEN 'DE 31 A 60 DIAS'
				WHEN DATEDIFF(CURDATE(), DATE(DataVenc))<=0 THEN 'ATE 30 DIAS'
				ELSE 'OUTRO' 
			END AS PeriodoVencimento	
			,VenctoCh AS VencimentoProrrogado        
			,CASE 
				WHEN DataVenc < CURDATE() AND ValorPagto IS NULL AND CodBanco IS NULL
				THEN (valor*0.02) 
				WHEN ValorPagto IS NOT NULL AND CodBanco IS NOT NULL
				THEN (valorpagto*0.02)
				ELSE 0
			END	AS Multa

			,CASE 
				WHEN DataVenc < CURDATE() AND ValorPagto IS NULL AND CodBanco IS NULL
				THEN (valor*0.0005*DATEDIFF(CURDATE(), DATE(DataVenc)))
				WHEN ValorPagto IS NOT NULL AND CodBanco IS NOT NULL
				THEN (valorpagto*0.0005*DATEDIFF(CURDATE(), DATE(DataVenc)))
				ELSE 0
			END AS Juros

			,CASE 
				WHEN DataVenc < CURDATE() AND ValorPagto IS NULL AND CodBanco IS NULL
				THEN valor+(valor*0.02)+(valor*0.0005*DATEDIFF(CURDATE(), DATE(DataVenc)))
				WHEN ValorPagto IS NOT NULL AND CodBanco IS NOT NULL
				THEN valorpagto+(valorpagto*0.02)+(valorpagto*0.0005*DATEDIFF(CURDATE(), DATE(DataVenc)))
				ELSE valor
			END
			AS ValorAtualizado			
			
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
					, fluxo.VenctoCh
					, fluxo.BaixaCh
					, fluxo.CodBanco
                    ,CASE 
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh IS NULL OR VenctoCh < '1900-01-01'))
                            THEN 'EM ATRASO'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01'))
                            THEN 'RENEGOCIADO A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01'))
                            THEN 'RENEGOCIADO EM ATRASO'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc >= CURRENT_date)
                            THEN 'A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01')
                            THEN 'CHEQUE A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') 
                            THEN 'CHEQUE DEVOLVIDO'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL  
                            THEN 'CHEQUE N?O BAIXADO'
                        ELSE 'OUTROS'
                    END AS Situacao
                    
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
					, fluxo.VenctoCh
					, fluxo.BaixaCh
					, fluxo.CodBanco
                    ,CASE 
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh IS NULL OR VenctoCh < '1900-01-01'))
                            THEN 'EM ATRASO'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01'))
                            THEN 'RENEGOCIADO A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01'))
                            THEN 'RENEGOCIADO EM ATRASO'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc >= CURRENT_date)
                            THEN 'A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01')
                            THEN 'CHEQUE A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') 
                            THEN 'CHEQUE DEVOLVIDO'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL  
                            THEN 'CHEQUE N?O BAIXADO'
                        ELSE 'OUTROS'
                    END AS Situacao
                
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
					, fluxo.VenctoCh
					, fluxo.BaixaCh
					, fluxo.CodBanco
                    ,CASE 
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh IS NULL OR VenctoCh < '1900-01-01'))
                            THEN 'EM ATRASO'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01'))
                            THEN 'RENEGOCIADO A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01'))
                            THEN 'RENEGOCIADO EM ATRASO'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc >= CURRENT_date)
                            THEN 'A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01')
                            THEN 'CHEQUE A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') 
                            THEN 'CHEQUE DEVOLVIDO'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL  
                            THEN 'CHEQUE N?O BAIXADO'
                        ELSE 'OUTROS'
                    END AS Situacao
                
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
					, fluxo.VenctoCh
					, fluxo.BaixaCh
					, fluxo.CodBanco
                    ,CASE 
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh IS NULL OR VenctoCh < '1900-01-01'))
                            THEN 'EM ATRASO'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01'))
                            THEN 'RENEGOCIADO A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01'))
                            THEN 'RENEGOCIADO EM ATRASO'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc >= CURRENT_date)
                            THEN 'A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01')
                            THEN 'CHEQUE A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') 
                            THEN 'CHEQUE DEVOLVIDO'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL  
                            THEN 'CHEQUE N?O BAIXADO'
                        ELSE 'OUTROS'
                    END AS Situacao
                
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
					, fluxo.VenctoCh
					, fluxo.BaixaCh
					, fluxo.CodBanco
                    ,CASE 
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh IS NULL OR VenctoCh < '1900-01-01'))
                            THEN 'EM ATRASO'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01'))
                            THEN 'RENEGOCIADO A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc < CURRENT_date AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01'))
                            THEN 'RENEGOCIADO EM ATRASO'
                        WHEN left(Tipo,2) = 'CR' AND (ValorPagto IS NULL AND DataVenc >= CURRENT_date)
                            THEN 'A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh >= CURRENT_date AND VenctoCh >= '1900-01-01')
                            THEN 'CHEQUE A VENCER'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL AND (VenctoCh < CURRENT_date AND VenctoCh >= '1900-01-01') 
                            THEN 'CHEQUE DEVOLVIDO'
                        WHEN left(Tipo,2) = 'CR' AND ValorPagto IS NOT NULL AND CodBanco IS NOT NULL AND BaixaCh IS NULL  
                            THEN 'CHEQUE N?O BAIXADO'
                        ELSE 'OUTROS'
                    END AS Situacao
                
                FROM Financeiro_Tecnologia.fluxo 
                JOIN Financeiro_Tecnologia.cli ON cli.Cod = fluxo.Fornecedor
                
        )fluxo_unificado

        WHERE 1=1 
        $data_rel
        AND DataPagto is null
        AND Tipo = '$tipo'	
        AND Razao = '$Razao'
        $sit
        ORDER BY razao	
        
    ";
    $y_venc = mysqli_query($con_mysql,$sql_venc);
     /*
	  echo '<pre>';	
		print_r($sql_venc);
      echo '</pre>';	
    */
    $cli='';
    echo'<table  class="table table-bordered table-condensed table-hover">';
        echo utf8_encode('
        <thead>
        <tr bgcolor="#c3c3c3">
            <th class="col-md-3">Empresa</th>
            <th class="col-md-3">Sit.</th>
            <th class="col-md-5">Cliente</th>
            <th class="col-md-2">Nf</th>
            <th class="col-md-3">Valor</th>
            <th class="col-md-3">Vencto</th>
            <th class="col-md-3">Reneg.</th>
            <th class="col-md-3">Atraso</th>
            <th class="col-md-3">Multa</th>
            <th class="col-md-3">Juros</th>
            <th class="col-md-3">Atualizado</th>
			
        </tr>
        </thead>
        <tbody>
        ');
    
    while($x = mysqli_fetch_array($y_venc)){
        $total = $total +$x['Valor'];
        $total_multa = $total_multa +$x['Multa'];
        $total_juros = $total_juros +$x['Juros'];
        $total_atualizado = $total_atualizado +$x['ValorAtualizado'];
        echo utf8_encode('
            <tr>
                <td class="col-md-3" ><b>'.$x['Empresa'].'</b></td>
                <td class="col-md-3" ><b>'.$x['Situacao'].'</b></td>
                <td class="col-md-5"><i>'.$x['EmpCli'].'</i></td>
                <td class="col-md-2" align="center">'.$x['NF'].'</td>
                <td class="col-md-3" align="right">'.number_format($x['Valor'],2,',','.').'</td>
                <td class="col-md-3" align="center">'.implode('/',array_reverse(explode('-',$x['DataVenc']))).'</td>
                <td class="col-md-3" align="center">'.implode('/',array_reverse(explode('-',$x['VencimentoProrrogado']))).'</td>
                <td class="col-md-3" align="right">'.number_format($x['DiasAberto'],0,',','.').'</td>
                <td class="col-md-3" align="right">'.number_format($x['Multa'],2,',','.').'</td>
                <td class="col-md-3" align="right">'.number_format($x['Juros'],2,',','.').'</td>
                <td class="col-md-3" align="right">'.number_format($x['ValorAtualizado'],2,',','.').'</td>
            </tr>
        ');
    };
    echo utf8_encode('
    </tbody>
    <tfoot>
    <tr bgcolor="#c3c3c3">
        <td class="col-md-4" colspan="4" >Total</td>
        <td class="col-md-3" align="right">'.number_format($total,2,',','.').'</td>
        <td class="col-md-3" colspan="3" ></td>
        <td class="col-md-3" align="right">'.number_format($total_multa,2,',','.').'</td>
        <td class="col-md-3" align="right">'.number_format($total_juros,2,',','.').'</td>
        <td class="col-md-3" align="right">'.number_format($total_atualizado,2,',','.').'</td>
    </tr>
    </tfoot>
');
echo'
        
    </table>';
    } 
    catch (Exception $ex)
    {
        //retorna 0 para no sucesso do ajax saber que foi um erro
        echo "0";
    }
?>