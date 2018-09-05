<?php
    include('../conectar.php');

    $cod_prod = $_GET['cod'];
    $quant = $_GET['quant'];

    $insert = "INSERT INTO possui(cod_pedido, cod_prod, quantidade_possui) VALUES(1600001, '$cod_prod', '$quant');";

    pg_query($conexao, $insert);
?>