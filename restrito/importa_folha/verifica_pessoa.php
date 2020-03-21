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
            <div class="">
                <div class="panel-body">
                <table class="table table-border table-condensed">
                    <?php
                        $handle = fopen ($arq ,'r');
                        while (($data = fgetcsv($handle, 1000, "#")) !== false) 
                        {
                          $linha = str_replace('"','',$data[0]);
                          $dados[] = explode(";", $linha);
                        }
                          foreach ($dados as $dados1) {
                            $Cod_func[]=array( 
                              preg_replace('/[^0-9]/', '', $dados1[0]),
                              preg_replace('/[^0-9]/', '', $dados1[1]),
                              trim(preg_replace('/[0-9]/', '', (trim(explode(':',$dados1[1])[1]))))
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
                        sort($Cod_func);//ordena Array
                        $Cod_func=super_unique($Cod_func,1);//Remove os duplicados
                        /*
                        echo'<pre>';
                          print_r($Cod_func);
                        echo'<pre>';
                        */
                        foreach($Cod_func as $result){
                          $sql_pessoa="select CodPessoaFolha from eventos_rh.pessoa where Empresa = '$result[0]' and CodPessoaFolha = $result[1]" ;
                          //echo $sql_pessoa.'<br>';
                          $y=mysqli_query($con_mysql,$sql_pessoa);
                          $rowcount=mysqli_num_rows($y);
                          if($rowcount==0){
                            $cod_emp      = $result[0];
                            $cod_func     = $result[1];
                            $funcionario  = $result[2];
                            //$nome_func = substr($funcionario,strpos($funcionario,$cod_func)+strlen($cod_func)-);
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
                                    <td>'.$funcionario.'</td>
                                    <td>'.$status.'</td>
                                </tr>
                              ');
                          }
                        }
                        
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