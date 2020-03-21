<style>
    table, th, td {
        font-size: 12px;
        font-family: "Calibri";
    }
</style>
<?php
	include('../../class/connect.php');
    $total=0;
    $Razao        = trim(utf8_decode($_REQUEST['cliente']));
    $tipo           = utf8_decode('Crédito'); 
    $data_ini       = $_REQUEST['data_ini'];
    $data_fin       = $_REQUEST['data_fin'];

    try
    {
        //consulta
        $sql_venc = "
        SELECT 
            Id
            , Cliente
            , DataContato
            , OBS
            , DataRetorno
        FROM financeiro_cobranca.cobranca
        WHERE Cliente = '$Razao'
        AND DataContato BETWEEN '$data_ini' AND '$data_fin'         
        ";
    // echo $sql_venc;   
    $y_venc = mysqli_query($con_mysql,$sql_venc);
    $cli='';
    echo'<table  class="table table-bordered table-condensed table-hover">';
        echo utf8_encode('
        <thead>
        <tr bgcolor="#c3c3c3">
            <th class="col-md-1"></th>
            <th class="col-md-3">Cliente</th>
            <th class="col-md-2">Data Contato</th>
            <th class="col-md-5">Obs</th>
            <th class="col-md-2">Data Retorno</th>
        </tr>
        </thead>
        <tbody>
        ');

    while($x = mysqli_fetch_array($y_venc)){
		$cli  = $x['Cliente'];
		$data = $x['DataContato'];
		$dtf  = $x['DataRetorno'];
		$obs  = $x['OBS'];
		$Id	  = $x['Id'];
        echo utf8_encode('
            <tr>
				<td class="col-md-1" align="center">
					<button type="button" title="Aterar Contato" class="btn btn-primary" 
							data-toggle="modal" data-target="#ModalInsercao" data-whatever_cliente="'.$cli.'"
							data-toggle="modal" data-target="#ModalInsercao" data-whatever_data="'.$data.'"
							data-toggle="modal" data-target="#ModalInsercao" data-whatever_data_fin="'.$dtf.'"
							data-toggle="modal" data-target="#ModalInsercao" data-whatever_obs="'.$obs.'"
							data-toggle="modal" data-target="#ModalInsercao" data-whatever_id="'.$Id.'"
							data-toggle="modal" data-target="#ModalInsercao" data-whatever_altera="S"
							data-dismiss="modal"
					>
						<i class="glyphicon glyphicon-pencil"></i>
					</button>
                </td>
				<td class="col-md-3"><i>'.$x['Cliente'].'</i></td>
                <td class="col-md-2" align="center">'.implode('/',array_reverse(explode('-',$x['DataContato']))).'</td>
                <td class="col-md-5">'.nl2br($x['OBS']).'</td>
                <td class="col-md-2" align="center">'.implode('/',array_reverse(explode('-',$x['DataRetorno']))).'</td>
            </tr>
        ');
    };
    echo utf8_encode('
        </tbody>
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