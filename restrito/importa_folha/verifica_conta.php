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
                        };
                        fclose($arquivo);

                        /*
                        echo'<pre>';
                        print_r($dados);
                        echo'</pre>';
                        */

                        foreach ($dados as $dados1) {
                          $doc_conta[] =array( 
                            preg_replace('/[^0-9]/', '', $dados1[0]),
                            preg_replace('/[^0-9]/', '', $dados1[3]),
                            $dados1[4]
                        );
                        };
                        
                        
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
                      sort($doc_conta);//ordena Array
                      $doc_conta=super_unique($doc_conta,1);//Remove os duplicados
                      /*
                      echo'<pre>';
                      print_r($doc_conta);
                      echo'</pre>';
                      */
                      

                      foreach($doc_conta as $result){
                        $sql_conta="select CodEvento from eventos_rh.eventos where Empresa = '$result[0]' and CodEvento = $result[1]" ;
                        //echo $sql_conta.'<br>';
                        $cod_emp 	    = $result[0];
                        $cod_tabela   = $result[1];
                        $tabela       = $result[2];

                        $y=mysqli_query($con_mysql,$sql_conta);
                        $rowcount=mysqli_num_rows($y);

                        if($rowcount==0 && $cod_tabela>0){
                          $status='
                                <button type="button" title="Assumir" class="btn btn-danger" 
                                    data-toggle="modal" data-target="#ModalAddConta" data-whatever_empresa="'.$cod_emp.'"
                                    data-toggle="modal" data-target="#ModalAddConta" data-whatever_CodEvento="'.$cod_tabela.'"
                                    data-toggle="modal" data-target="#ModalAddConta" data-whatever_Evento="'.$tabela.'"
                                >
                                    <span class="glyphicon glyphicon-th-list"></span>
                                </button
                            ';
                            echo utf8_encode('
                            <tr>
                                <td>'.$cod_tabela.'</td>
                                <td>'.$tabela.'</td>
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

<!-- ************************ Modal ModalAddConta *************************************** -->
<div class="modal fade" id="ModalAddConta" tabindex="-1" role="dialog" aria-labelledby=ModalAddContaLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="ModalInsercaoLabel">Cadastro de conta:</h4>
				</div>
			</div>
		</div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Empresa:</label>
            <input type="text" class="form-control" id="Empresa_conta" name="Empresa_conta" >
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Evento:</label>
            <input type="text" class="form-control" id="Evento" name="Evento" >
            <input type="hidden" class="form-control" id="CodEvento" name="CodEvento" >
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Cod. Conta:</label>
            <input type="text" class="form-control" id="CodConta" name="CodConta" >
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Cod. CC:</label>
            <input type="text" class="form-control" id="CodCC" name="CodCC" >
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Lançar?:</label>
              <select class="form-control" id="lancar" name="lancar" >
                  <option value = 'S'>Sim</option>
                  <option value = 'N'>Não</option>
              </select>
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Tipo:</label>
              <select class="form-control" id="Tipo" name="Tipo" >
                  <option value = 'DÉBITO'>Débito</option>
                  <option value = 'CRÉDITO'>Crédito</option>
              </select>
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" name="grava_conta" id="grava_conta" data-dismiss="modal">Gravar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
        $('#ModalAddConta').on('show.bs.modal', function (event) {
          var button = $(event.relatedTarget) // Button that triggered the modal
          var Empresa1 = button.data('whatever_empresa');
          var CodEvento1 = button.data('whatever_codevento');
          var Evento1 = button.data('whatever_evento');
          
          //console.log(button.data())

          var modal = $(this)
          modal.find('input#Empresa_conta').val(Empresa1)
          modal.find('input#CodEvento').val(CodEvento1)
          modal.find('input#Evento').val(Evento1)

        })
        $('#grava_conta').click(function(){
            var empresa = $('input#Empresa_conta').val();
            var CodEvento = $('input#CodEvento').val();
            var Evento = $('input#Evento').val();
            var Tipo = $('select#Tipo').val();
            var lancar = $('select#lancar').val();
            var CodConta = $('input#CodConta').val();
            var CodCC = $('input#CodCC').val();
            var Multiplicador = '';
            if(Tipo=="Crédito"){Multiplicador=1;}else{Multiplicador=-1;};


            var dadosajax={
                    'empresa' : empresa,
                    'CodEvento' : CodEvento,
                    'Evento' : Evento,
                    'Tipo' : Tipo,
                    'lancar' : lancar,
                    'CodConta' : CodConta,
                    'CodCC' : CodCC,
                    'Multiplicador' : Multiplicador

                };
                //console.log(dadosajax);
                pageurl = 'grava_evento.php';
                $.ajax({
                    url: pageurl,
                    data: dadosajax,
                    type: 'POST',
                    cache: false,
                    beforeSend: function(){
                          verificaConta()
                        },
                    success: function(result)
                    {  
                      console.log(result);
                    }
                })

        })
  })
</script>