<?php
    
    include('../conectar.php');

    $ped = $_GET['pedido'];
    $ped = pg_escape_string($ped);

    $query = "SELECT * FROM encomenda WHERE cod_pedido = '$ped';";

    $res = pg_query($conexao, $query);
    $nlinhas = pg_affected_rows($res);

    if($nlinhas > 0){
        $obj = pg_fetch_object($res);
        echo $obj->data_entrega;
    }else{
        echo -1;
    }
    
    pg_close();
?>