<?php
    
    include('../conectar.php');

    $ped = $_GET['pedido'];
    $ped = pg_escape_string($ped);

    $query = "SELECT * FROM pedido WHERE cod_pedido = $ped";

    $res = pg_query($conexao, $query);
    $nlinhas = pg_affected_rows($res);

    if($nlinhas > 0){
        $obj = pg_fetch_object($res);
        $envio .="Valor Total: R$ ".money_format('%.2n', $obj->preco_total);
    }else{
        $envio .="Valor Total: R$ ".money_format('%.2n', 0);
    }
    
    pg_close();
    echo $envio;
?>