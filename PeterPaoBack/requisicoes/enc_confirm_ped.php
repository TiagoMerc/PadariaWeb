<?php
    session_start();
    include('../conectar.php');

    $ped = $_SESSION['cod_pedido'];
    
    $ped = pg_escape_string($ped);

    $busca = "SELECT * FROM possui WHERE cod_pedido = '$ped';";

    $res = pg_query($conexao, $busca);
    $nlinhas = pg_affected_rows($res);
    
    if($nlinhas > 0){
        $cli = $_GET['cliente'];
        $cli = str_replace(".", "", $cli);
        $cli = str_replace("-", "", $cli);
        $cli = pg_escape_string($cli);
        
        $inf = "SELECT * FROM cliente WHERE cpf_cliente = '$cli';";
        $res = pg_query($conexao, $inf);
        
        if(pg_affected_rows($res) > 0){
            $obj = pg_fetch_object($res);
            
            if($obj->infrator == '1'){
                pg_close();
                echo -3;
            }else{
                $data_ent = $_GET['data'];
                $data_hoje = date("d/m/Y");
                $pago = $_GET['pago'];

                $data_ent = pg_escape_string($data_ent);
                $data_hoje = pg_escape_string($data_hoje);
                $pago = pg_escape_string($pago);

                $query = "INSERT INTO encomenda(cod_pedido, cpf_cliente, data_pedido, data_entrega, pago) VALUES($ped, '$cli' , '$data_hoje', '$data_ent', '$pago');";

                $nlinhas = pg_affected_rows(pg_query($conexao, $query));

                $_SESSION['cod_pedido'] = -1;
                $up = "UPDATE pedido SET data_venda = '$data_hoje' WHERE cod_pedido = $ped;";
                pg_query($conexao, $up);

                pg_close();
                echo $ped;   
            }
        }else{
            pg_close();
            echo -1;
        }
    }else{
        pg_close();
        echo -2;
    }
?>