<?php

    include('../conectar.php');

    $ped = $_GET['pedido'];
    $prod = $_GET['prod'];
    $modo = $_GET['modo'];

    $ped = pg_escape_string($ped);
    $prod = pg_escape_string($prod);

    //update
    if($modo == 1){ //alter
        $quant = $_GET['quant'];
        
        $query = "UPDATE possui SET quantidade_possui = '$quant' WHERE cod_pedido = '$ped' AND cod_prod = '$prod';";
        pg_query($conexao, $query); 
    }else if($modo == 2){ //delete
        $query = "DELETE FROM possui WHERE cod_pedido = '$ped' AND cod_prod = '$prod';";
        pg_query($conexao, $query);
    }

    pg_close();

?>