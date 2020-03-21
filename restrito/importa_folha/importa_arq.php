<?php
  session_start();
  include('../../class/connect.php');
  ini_set('display_errors',0);
  $cod_func_v = '';
  $arq  = $_POST['arq'];
  $importa='N';
  $data_vence = $_SESSION['data'];
  
  $handle = fopen ($arq ,'r');
  while (($data = fgetcsv($handle, 1000, "#")) !== false) 
  {
    $linha = str_replace('"','',$data[0]);
    $dados[] = explode(";", $linha);
  }
  
  fclose($arquivo);
  
  foreach ($dados as $dados1) {
    $folha[]=array(
      preg_replace('/[^0-9]/', '', $dados1[0]),
      preg_replace('/[^0-9]/', '', $dados1[1]),
      $dados1[3],
      $dados1[4],
      $dados1[7],
      $dados1[8],
      trim(preg_replace('/[0-9]/', '', (trim(explode(':',$dados1[1])[1]))))      
    );
  }


  foreach ($dados as $dados1) {
    $emp[]=array(
      preg_replace('/[^0-9]/', '', $dados1[0]),
    );
  }
  foreach ($dados as $dados1) {
    $pessoa[]=array(
      preg_replace('/[^0-9]/', '', $dados1[1])
    );
  }
  foreach ($dados as $dados1) {
    $conta[]=array(
      preg_replace('/[^0-9]/', '', $dados1[3])
    );
  }

  /***************função para remover valores duplicados no array*********/
    function super_unique($array,$key)
    {
      $temp_array = [];
      foreach ($array as &$v) {
          if (!isset($temp_array[$v[$key]]))
          $temp_array[$v[$key]] =& array_filter($v);
      }
      $array = array_values($temp_array);
      $array = array_filter($array);
      return $array;

    }
  /********************************************************************* */
  sort($emp);//ordena Array
  sort($pessoa);//ordena Array
  sort($conta);//ordena Array
  sort($folha);//ordena Array

  $emp=super_unique($emp,0);//Remove os duplicados
  $pessoa=super_unique($pessoa,0);//Remove os duplicados
  $conta=super_unique($conta,0);//Remove os duplicados

  foreach ($emp as $emp1) {
    $cod_emp = $emp1[0];
  }
  $falta_pessoa='N';
  foreach ($pessoa as $pessoa1) {
    $cod_pessoa = $pessoa1[0];
    $sql_pessoa="select CodPessoaFolha from eventos_rh.pessoa where Empresa ='$cod_emp' and CodPessoaFolha in ($cod_pessoa)" ;
    $y=mysqli_query($con_mysql,$sql_pessoa);
    $rowcount=mysqli_num_rows($y);  
    if($rowcount<=0){$falta_pessoa = 'S';};  
  }
  $falta_conta='N';
  foreach ($conta as $conta1) {
    $cod_conta = $conta1[0];
    $sql_conta="select CodEvento from eventos_rh.eventos where Empresa = '$cod_emp' and CodEvento = $cod_conta" ;
    $y=mysqli_query($con_mysql,$sql_conta);
    $rowcount=mysqli_num_rows($y);  
    if($rowcount<=0){$falta_conta = 'S';};  
  }


  echo 'Falta Pessoa?:'.$falta_pessoa.'<br>';
  echo 'Falta Conta?:'.$falta_conta.'<br>';

/*
  echo '<pre>';
    print_r($folha);
  echo '</pre>';
*/

if($falta_pessoa=='N' && $falta_conta=='N'){
  foreach($folha as $result){
    if($result[0]>0){
      switch ($result[0]) {
        case "0001":
          $base='financeiro_contabilidade';
          break;
        case "0002":
          $base='Financeiro_Planner';
          break;
        case "":
          $base='Financeiro_assessoria';
          break;
        case "1500":
          $base='Financeiro_timbo';
          break;
        case "":
          $base='Financeiro_Tecnologia';
          break;
      } //fim do  switch ($result[0])
      // verificando se a pessoa sera importada
      $cod_emp=$result[0];
      $cod_conta  = $result[2];
      $periodo = $result[5];

      if($cod_pessoa!=$result[1]){
        $cod_pessoa=$result[1];
        $nome_pessoa=$result[6];
       
        $sql_pessoa="select CodPessoaFinanceiro from eventos_rh.pessoa where Empresa ='$cod_emp' and CodPessoaFolha in ($cod_pessoa) and importar = 'S'" ;
        $y=mysqli_query($con_mysql,$sql_pessoa);
        $CodPessoaFinanceiro=0;
        while($x=mysqli_fetch_array($y)){$CodPessoaFinanceiro =$x['CodPessoaFinanceiro'];};
        if($CodPessoaFinanceiro>0){
          
          $dados_para_folha[$CodPessoaFinanceiro][]=$nome_pessoa;
          $dados_para_folha[$CodPessoaFinanceiro][]=$result[5];
        }
      }

        
        $sql_conta="select CodConta,CodCC,Tipo from eventos_rh.eventos where Empresa = '$cod_emp' and CodEvento = $cod_conta and lancar='S'" ;
        $CodConta=0;
        $y=mysqli_query($con_mysql,$sql_conta);
        while($x=mysqli_fetch_array($y)){
          $CodConta =$x['CodConta'];
          $CodCC =$x['CodCC'];
          $Tipo =$x['Tipo'];
        };
        if($CodConta>0 && $CodPessoaFinanceiro>0){
          $dados_para_folha[$CodPessoaFinanceiro][]='Lancamento->'.$CodCC.'->'.$CodConta.'->'.$result[3].'->'.$result[4].'->'.$Tipo;
        }
    }//fim do if($result[0]>0)
  }// fim do foreach
}//fim do if($falta_pessoa=='N' && $falta_conta=='N')
/*
echo '<pre>';
  print_r($dados_para_folha);
echo '</pre>';
*/
asort($dados_para_folha);
foreach ($dados_para_folha as $Cod_func_1 => $dados_eventos)
{
  $Fornecedor=$Cod_func_1;
  $html='<table class="table table-bordered table-hover">';
  $html.='<tr>
              <td colspan="6"  align="center" bgcolor="#c3c3c3"><h4>Folha</h4></td>
          </tr>
  ';
  $Total_func=0;

  foreach($dados_eventos as $evento){
      $evento_ = explode('->',$evento);
      if($evento_[0]!='Lancamento'){
          $html.=utf8_encode("
            <tr>
              <td colspan='6'>$evento_[0]</td>
            </tr>
        ");
  }else{
          $Tipo = utf8_encode($evento_[5]);
          if($Tipo=='CRÉDITO'){
            $valor = str_replace(',','.',str_replace('.','',$evento_[4]));
          }else{
            $valor = str_replace(',','.',str_replace('.','',$evento_[4]))*-1;
          }
          $Total_func+=$valor;
          $Total+=$valor;
          $CodCa = $evento_[1];
          $Desp = $evento_[2];
          $NF = preg_replace('/[^0-9]/', '', $periodo);
          $Dpl = $periodo;
          $Doc = $periodo;

          $Dados_inserir[]=array(
                                  'Tipo'=>$Tipo
                                  ,'Fornecedor'=>$Fornecedor
                                  ,'NF'=>$NF
                                  ,'Dpl'=>$Dpl
                                  ,'Doc'=>$Doc
                                  ,'Desp'=>$Desp
                                  ,'CodCa'=>$CodCa
                                  ,'valor'=>$valor
                                  ,'data_vence'=>$data_vence
                                  ,'periodo'=>$periodo
                                  ,'base'=>$base
                                );



            $html.=utf8_encode("
            <tr>
              <td>$evento_[0]</td>
              <td>$evento_[1]</td>
              <td>$evento_[2]</td>
              <td>$evento_[3]</td>
              <td align='right'>".number_format($valor,2,',','.')."</td>
              <td>$evento_[5]</td>
            </tr>
        ");
    }
  }
$html.=utf8_encode("
<tr>
  <td colspan='5'>Total</td>
  <td align='right'>".number_format($Total_func,2,',','.')."</td>
</tr>
");

$html.='</table><br>';
  echo $html;
};
echo '
<div class="container">
  <div class="row">
    <div class="">
        <div class="panel-default panel panel-danger">
            <div class="panel-heading" align="center">
                <h2> Valor Líquido total = '.number_format($Total,2,',','.').'</h2>
            </div>
        </div>
        <div class="panel-body">
            <fildset>
                <div class="form-group col-md-12">
                    <input name="importa" id="importa" class="btn btn-danger btn-block" type="button" value="Importar" onclick="importar();">              
                </div>
            </fildset>
        </div>
    </div>  
  </div>
</div>
';


?>
<script>
  function importar(){
    var sql = <?php echo json_encode( $Dados_inserir ) ?>;
    var dadosajax={
            'sql' : sql
        };
        pageurl = 'importar_financeiro.php';
        $.ajax({
            url: pageurl,
            data: dadosajax,
            type: 'POST',
            cache: false,
            beforeSend: function(){
                    $('#modal_aguarde_importacao').modal('show');
                },
            success: function(result)
            {  
              console.log(result);
              $('#modal_aguarde_importacao').modal('hide');
              if(result==1){
                $('#modal_sucesso').modal('show');

                setTimeout(function() {
                  $('#modal_sucesso').modal('hide');
                }, 3000); // 3000 = 3 segundos
              }else{
                $('#modal_danger').modal('show');
                setTimeout(function() {
                  $('#modal_danger').modal('hide');
                }, 3000); // 3000 = 3 segundos
              }
            }
        })

  }  
</script>
<div class="modal fade" id="modal_aguarde_importacao">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="alert alert-success" align="center">Aguarde!!!!</h2>
        </div>
        <div class="modal-body" align="center">
          <h4>Incluindo no Financeiro</h4>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="modal_sucesso">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="alert alert-success" align="center">Sucesso</h2>
        </div>
        <div class="modal-body" align="center">
          <h4>Inclusão no Financeiro com sucesso!!!!</h4>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="modal_danger">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="alert alert-danger" align="center">Erro</h2>
        </div>
        <div class="modal-body" align="center">
          <h4>Documentos não processados!!!!</h4>
        </div>
      </div>
    </div>
</div>