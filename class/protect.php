<?php
if(!function_exists("protect")){

  function protect(){

    if(!isset($_SESSION['usuario_logado'])){

      
      echo "<script>location.href='index.php';</script>";
      exit('Login inválido: Redirecionando...');

    }

  }

}

protect();
?>