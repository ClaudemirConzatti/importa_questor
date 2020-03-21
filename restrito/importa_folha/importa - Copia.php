<?php

  include('../../class/connect.php');
  ini_set('display_errors',0);
  $cod_func_v = '';
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>        

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
                    <h3 class="panel-danger">Retorno</h3>
                </div>
                <div class="panel-body">
                <table class="table table-border table-condensed">
                  <tr>
                    <td>Empresa</td>
                    <td>Cod.Funcionario</td>
                    <td>Funcionario</td>
                    <td>Período</td>
                    <td>Cód.Tabela</td>
                    <td>Tabela Contábil</td>
                    <td>Valor</td>
                    <td>Complemento</td>
                  </tr>
                <?php
                    if (isset($_FILES)) {
                        $arquivoUpload = $_FILES['file_csv'];
                        move_uploaded_file($arquivoUpload['tmp_name'],'uploads/'.$arquivoUpload['name']);
                        $arq = 'uploads/'.$arquivoUpload['name'];
                        $handle = fopen ($arq ,'r');


                        while (($data = fgetcsv($handle, 1000, "#")) !== false) 
                        {
                          $i++;
                          $num = count ($data);
                          for ($c=0; $c < $num; $c++) {
                            $dados = str_replace('"','',$data[0]);
                            $linha = explode(";", $dados);
                            $cod_emp      = preg_replace('/[^0-9]/', '', $linha[0]);

                            switch ($cod_emp) {
                              case "0001":
                                $base='financeiro_contabilidade';
                                break;
                              case "0002":
                                $base='Financeiro_Planner';
                                break;
                              case "":
                                $base='Financeiro_assessoria';
                                break;
                              case "":
                                $base='Financeiro_timbo';
                                break;
                              case "":
                                $base='Financeiro_Tecnologia';
                                break;
                            }                            

                            $empresa 	    = explode('-',$linha[0]);
                            $empresa 	    = $empresa[1];
                            $funcionario 	= $linha[1];
                            $cod_func     = preg_replace('/[^0-9]/', '', $linha[1]);
                            $nome_func    = substr($funcionario,strpos($funcionario,$cod_func)+strlen($cod_func));
                            $periodo 	    = $linha[2];
                            $cod_tabela 	= $linha[3];
                            $tabela 	    = $linha[4];
                            $valor 	      = $linha[7];
                            $complemento 	= $linha[8];
                            if($cod_emp>='0001'){
                               if($cod_func_v!=$cod_func){
                                  $cod_func_v=$cod_func;
                                  $sql_pessoa="select CodPessoaFolha from eventos_rh.pessoa where Empresa = '$cod_emp' and CodPessoaFolha = $cod_func" ;
                                  $y=mysqli_query($con_mysql,$sql_pessoa);
                                  $rowcount=mysqli_num_rows($y);
                                  if($rowcount==0){

                                  }
                               };
                              echo utf8_encode('
                                <tr>
                                    <td>'.$cod_emp.'-'.$empresa.'</td>
                                    <td>'.$cod_func.'</td>
                                    <td>'.$nome_func.'</td>
                                    <td>'.$periodo.'</td>
                                    <td>'.$cod_tabela.'</td>
                                    <td>'.$tabela.'</td>
                                    <td>'.$valor.'</td>
                                    <td>'.$complemento.'</td>
                                </tr>
                              ');
                            };//fim do if
                          };
                        };
                      };
                        fclose($arquivo);
                    ?>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>    

  </body>
</html>
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
<script>
  $(document).ready(function(){  
    $('#modal_aguarde').modal('show');

  })

</script>
