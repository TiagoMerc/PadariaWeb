<?php
    include('../conectar.php');

    $cod_prod = $_GET['cod'];
    $quant = $_GET['quant'];
    $ped = $_GET['pedido'];

    $cod_prod = pg_escape_string($cod_prod);
    $quant = pg_escape_string($quant);
    $ped = pg_escape_string($ped);

    $busca = "SELECT * FROM possui WHERE cod_pedido = '$ped' AND cod_prod = '$cod_prod';";
    $result = pg_query($conexao, $busca);
    $nlinhas = pg_affected_rows($result);

    if($nlinhas == 0){
        $insert = "INSERT INTO possui(cod_pedido, cod_prod, quantidade_possui) VALUES('$ped', '$cod_prod', '$quant');";

        $result = pg_query($conexao, $insert);
        $nlinhas = pg_affected_rows($result);

        if($nlinhas > 0){
            echo "Produto adicionado com sucesso!";
        }else{
            echo "Produto não foi adicionado.";
        }
    }else{
        $obj = pg_fetch_object($result);
        $q = $quant;
        $quant += $obj->quantidade_possui;
        
        $update = "UPDATE possui SET quantidade_possui = '$quant' WHERE cod_pedido = '$ped' AND cod_prod = '$cod_prod';";
        pg_query($conexao, $update);
        
        echo "Quantidade do produto incrementeda em ".$q." unidades";
    }    

    pg_close();
?>