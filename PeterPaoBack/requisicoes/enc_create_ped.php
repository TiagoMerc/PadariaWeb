<?php
    
    include('../conectar.php');

    //inicia inserção e criação do pedido
    $cpf_funcio = $_GET['cpf'];
    $data = "01/01/0001";
    
    $cpf_funcio = pg_escape_string($cpf_funcio);
    $data = pg_escape_string($data);

    $insert = "INSERT INTO pedido(cpf_funcio, preco_total, data_venda) VALUES('$cpf_funcio', 0, '$data') RETURNING cod_pedido;";
    $result = pg_query($conexao, $insert);

    //pega o número do pedido retornado
    $obj = pg_fetch_object($result);
    echo $obj->cod_pedido;

    pg_close();

?>