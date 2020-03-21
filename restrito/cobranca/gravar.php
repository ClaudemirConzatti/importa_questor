<?php
	include('../../class/connect.php');

    $Cliente        = trim(utf8_decode($_REQUEST['Cliente']));
    $DataRetorno    = $_REQUEST['DataRetorno'];
    $DataContato    = $_REQUEST['DataContato'];
    $data_fin       = $_REQUEST['data_fin'];
    $ObsCob         = utf8_decode($_REQUEST['ObsCob']);
    $Altera			= $_REQUEST['Altera'];
	$Id				= $_REQUEST['Id'];

    try
    {
        //insere na BD
        if(trim($Altera!='S')){
            $sql = "
                INSERT INTO financeiro_cobranca.cobranca (Cliente, DataContato, OBS, DataRetorno)
                VALUES ('$Cliente', '$DataContato', '$ObsCob', '$DataRetorno')
            ";
            mysqli_query($con_mysql,$sql);
        }else{
            $sql = "
                UPDATE financeiro_cobranca.cobranca 
					SET Cliente			= '$Cliente'
						, OBS			= '$ObsCob'
						, DataRetorno	= '$DataRetorno'
                WHERE Id = $Id
            ";
            mysqli_query($con_mysql,$sql);
		}
        /******************Atualizando as bases********************************/
            for($i = 1; $i < 5; $i++){
                if($i==1){
                    $base = 'financeiro_planner';
                }elseif($i==2){
                    $base = 'financeiro_contabilidade';
                }elseif($i==3){
                    $base = 'Financeiro_timbo';
                }elseif($i==4){
                    $base = 'Financeiro_Tecnologia';
                }elseif($i==5){
                    $base = 'Financeiro_assessoria';
                }
                $sql_upd = "
                    UPDATE $base.fluxo
                    JOIN $base.cli ON cli.Cod = fluxo.Fornecedor
                        SET DataRetorno = '$DataRetorno'
                    WHERE 1 = 1
                    AND (CASE WHEN LTRIM(RTRIM(Nome)) <> '' THEN Nome ELSE Razao END) = '$Cliente'
                    AND fluxo.DataPagto IS NULL 
                    AND DataVenc <= '$data_fin'
                ";
                mysqli_query($con_mysql,$sql_upd);
            }//fim do for	
			
            echo "1";
    } 
    catch (Exception $ex)
    {
        //retorna 0 para no sucesso do ajax saber que foi um erro
        echo "0";
    }
?>