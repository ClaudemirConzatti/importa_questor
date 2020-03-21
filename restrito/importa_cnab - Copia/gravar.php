<?php
    include('../../class/conexao.php');
    //odbc_autocommit($con_fb, FALSE);
	//recebe os parâmetros

    $codProd= $_REQUEST['codProd'];
    $custo  = $_REQUEST['custo'];
    $custo = str_replace('.','',$custo);
    $custo = str_replace(',','.',$custo);
    $CNPJ   = $_REQUEST['CNPJ'];

/*
    $grava = $_REQUEST['grava'];
    if($grava=='S'){
        odbc_commit($con_fb);
        odbc_close($con_fb);
        echo $grava;
    }elseif($grava=='N'){
        odbc_rollback($con_fb);
        odbc_close($con_fb);
        echo $grava;
    }
*/
    
    
    try
    {
        //insere na BD
        if(trim($grava=='')){
            $sql = "
            SELECT 
                first 1
                PRODUTO_FORNECEDOR.CD_REF
                ,PRODUTO.DS_PROD
                ,SUB_TAB_PRECO.CD_TABELA
                ,SUB_TAB_PRECO.VL_CUSTO
                    
            FROM PRODUTO_FORNECEDOR 
            JOIN PESSOA ON PESSOA.CD_PESSOA = PRODUTO_FORNECEDOR.CD_FORNECEDOR
            JOIN SUB_TAB_PRECO ON SUB_TAB_PRECO.CD_REF = PRODUTO_FORNECEDOR.CD_REF
            JOIN PRODUTO ON PRODUTO.CD_REF = PRODUTO_FORNECEDOR.CD_REF
            WHERE REPLACE(REPLACE(REPLACE(PESSOA.CD_CGCCPF,'.',''),'/',''),'-','') = '$CNPJ'
            AND CD_REF_FORNECEDOR = '$codProd'       
            ";
            $y = odbc_exec($con_fb,$sql);
            while($x = odbc_fetch_array($y)){
                $CD_REF = $x['CD_REF'];
                $sql_upd = "UPDATE SUB_TAB_PRECO SET VL_CUSTO = $custo WHERE CD_REF = '$CD_REF'";
                $res = odbc_exec($con_fb, $sql_upd);
                if (!$res){
                    print("SQL statement failed with error:\n");
                    print(odbc_error($con_fb).": ".odbc_errormsg($con_fb)."\n");
                } 
            }
            echo "1";
            /*
            echo $sql.'/n';
            echo $sql_upd;
            echo $grava;
            */
        }
    } 
    catch (Exception $ex)
    {
        //retorna 0 para no sucesso do ajax saber que foi um erro
        echo "0";
        
    }
?>