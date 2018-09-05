<?php

    include('../conectar.php');

    $ped = $_GET['pedido'];
    $data = $_GET['data'];
    $pago = $_GET['pago'];

    $ped = pg_escape_string($ped);
    $data = pg_escape_string($data);
    $pago = pg_escape_string($pago);

    $busca = "SELECT * FROM possui WHERE cod_pedido = '$ped';";

    $res = pg_query($conexao, $busca);
    $nlinhas = pg_affected_rows($res);

    if($nlinhas == 0){
        echo -2;
    }else{
        $query = "UPDATE encomenda SET pago = '$pago', data_entrega = '$data' WHERE cod_pedido = '$ped';";
        $nlinhas = pg_affected_rows(pg_query($conexao, $query)); 

        if($nlinhas == 0){
            echo 0;
        }else{
            echo 1;
        }
    }
    

    pg_close();

?>