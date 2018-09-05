<?php
    $servidor = "localhost";
    $usuario = "root";
    $senha = "mysql";
    $BD = "padaria";
        
    $conexao = new mysqli($servidor, $usuario, $senha, $BD);

    if ($conexao->connect_error) {
        die("Falha de conexão: " . $conexao->connect_error);
    }  
?>