<?php

    include('../conectar.php');
    session_start();

    //tira o cookie
    setcookie('value_p', '', 1);

    //resgata pedido na sessão
    if(isset($_GET['pedido'])){
        $ped = $_GET['pedido'];
    }else{
        $ped = $_SESSION['cod_pedido'];   
    }

    $ped = pg_escape_string($ped);
    
    $delete = "DELETE FROM pedido WHERE cod_pedido = $ped;";

    $result = pg_query($conexao, $delete);
    $nlinhas = pg_affected_rows($result);
    
    if($nlinhas > 0){
        //tira o cookie
        setcookie('value_p', '', 1);
        
        //tira da sessão
        $_SESSION['cod_pedido'] = -1;
        
        echo 1;
    }else{
        echo 0;
    }
?>