<?php

  include('../../class/connect.php');
  ini_set('display_errors',0);
  $cod_func_v = '';
  $arq  = $_POST['arq'];
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

        <!-- Bootstrap Core CSS -->
        <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
        <script  src="../../bootstrap/css/jquery.min.js"></script>
        <script  src="../../bootstrap/css/bootstrap.min.js"></script>
        <script src="js/jquery-3.1.1.min.js"></script>
  	    <script src="js/bootstrap.min.js"></script>

  <title>Verifica Pessoa</title>

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
                <div class="panel-body">
                <table class="table table-border table-condensed">
                  <tr>
                    <td>Cod.Funcionario</td>
                    <td>Funcionario</td>
                  </tr>
                <?php
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
                              case "1500":
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
                                  $sql_pessoa="select CodPessoaFolha from eventos_rh.pessoa where Empresa = $cod_emp and CodPessoaFolha = $cod_func" ;
                                  //echo $sql_pessoa.'<br>';
                                  $y=mysqli_query($con_mysql,$sql_pessoa);
                                  $rowcount=mysqli_num_rows($y);
                                  if($rowcount==0){
                                      $status='
                                            <button type="button" title="" class="btn btn-danger" 
                                                data-toggle="modal" data-target="#ModalAdd" data-whatever_empresa="'.$cod_emp.'"
                                                data-toggle="modal" data-target="#ModalAdd" data-whatever_codpessoafolha="'.$cod_func.'"
                                                data-toggle="modal" data-target="#ModalAdd" data-whatever_CodPessoaFinanceiro=""
                                            >
                                                <span class="glyphicon glyphicon-user"></span>
                                            </button
                                        ';
                                        echo utf8_encode('
                                        <tr>
                                            <td>'.$cod_func.'</td>
                                            <td>'.$nome_func.'</td>
                                            <td>'.$status.'</td>
                                            <td>'.$input.'</td>
                                        </tr>
                                      ');
                                  };
                               };
                            };//fim do if
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

<!-- ************************ Modal ModalAdd *************************************** -->
<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="ModalAddLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="ModalInsercaoLabel">Cadastrar Funcionario:</h4>
				</div>
			</div>
		</div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Empresa:</label>
            <input type="text" class="form-control" id="Empresa" name="Empresa" >
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Cod. Pessoa na Folha:</label>
            <input type="number" class="form-control" id="CodPessoaFolha" name="CodPessoaFolha" >
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Cod. Pessoa Financeiro:</label>
            <input type="number" class="form-control" id="CodPessoaFinanceiro" name="CodPessoaFinanceiro" >
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Importa?:</label>
              <select class="form-control" id="importar" name="importar" >
                  <option value = 'S'>Sim</option>
                  <option value = 'N'>Não</option>
              </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" name="grava_pessoa" id="grava_pessoa" data-dismiss="modal">Gravar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
        $('#ModalAdd').on('show.bs.modal', function (event) {
          var button = $(event.relatedTarget) // Button that triggered the modal
          var Empresa1 = button.data('whatever_empresa');
          var CodPessoaFolha1 = button.data('whatever_codpessoafolha');
          
          console.log(button.data())

          var modal = $(this)
          modal.find('input#Empresa').val(Empresa1)
          modal.find('input#CodPessoaFolha').val(CodPessoaFolha1)

        })
        $('#grava_pessoa').click(function(){
            var empresa = $('input#Empresa').val();
            var CodPessoaFolha = $('input#CodPessoaFolha').val();
            var CodPessoaFinanceiro = $('input#CodPessoaFinanceiro').val();
            var importar = $('select#importar').val();
            var dadosajax={
                    'empresa' : empresa,
                    'CodPessoaFolha' : CodPessoaFolha,
                    'CodPessoaFinanceiro' : CodPessoaFinanceiro,
                    'importar' : importar
                };
                //console.log(dadosajax);
                pageurl = 'grava_pessoa.php';
                $.ajax({
                    url: pageurl,
                    data: dadosajax,
                    type: 'POST',
                    cache: false,
                    beforeSend: function(){
                            verificaPessoa()
                        },
                    success: function(result)
                    {  
                      console.log(result);
                    }
                })

        })
  })
</script>