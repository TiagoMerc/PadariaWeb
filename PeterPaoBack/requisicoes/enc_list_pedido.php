<?php
    include('../conectar.php');

    $ped = $_GET['pedido'];
    $modo = $_GET['modo'];

    $ped = pg_escape_string($ped);

    if($ped == -1){
        $envio = "<tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th> 
                        <th>Excluir</th>
                     </tr>";
    }else{

        $query = "SELECT * FROM possui WHERE cod_pedido = '$ped';";
        $result = pg_query($conexao, $query);
        $nlinhas = pg_affected_rows($result);

        $envio = "<tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th> 
                        <th>Excluir</th>
                     </tr>";

        if($nlinhas > 0){
            while($obj = pg_fetch_object($result)){
                $envio .="<tr>";
                $envio .="<td>$obj->cod_prod</td>";

                //pesquisa para conseguir dados do produto
                $query = "SELECT * FROM produto WHERE cod_prod = '$obj->cod_prod';";
                $res = pg_query($conexao, $query);
                $prod = pg_fetch_object($res);

                $envio .="<td style='text-align:left; padding-left:10px'>$prod->nome_prod</td>"; 
                
                if($modo == '1'){
                    $envio .="<td><input class=\"botaoAddQtdoEnco\" onchange=\"update_quanti($obj->cod_prod, '1')\" type=\"number\" value=\"".$obj->quantidade_possui."\" id=\"quantList".$obj->cod_prod."\"></input></td>";
                }else{
                    $envio .="<td><input class=\"botaoAddQtdoEnco\" onchange=\"update_quanti($obj->cod_prod, '2')\" type=\"number\" value=\"".$obj->quantidade_possui."\" id=\"quantListAlt".$obj->cod_prod."\"></input></td>";
                }

                $envio .="<td style='text-align:left; padding-left:15px'>R$ ". money_format('%.2n', $prod->preco_unitario). "</td>";
                
                if($modo == '1'){
                    $envio .= "<td> <button class=\"cancelar\" onclick=\"delete_prod($obj->cod_prod, '1')\" type=\"button\"> <i class=\"fa fa-close\" style=\"font-size:20pt\"></i> </button> </td>";
                }else{
                    $envio .= "<td> <button class=\"cancelar\" onclick=\"delete_prod($obj->cod_prod, '2')\" type=\"button\"> <i class=\"fa fa-close\" style=\"font-size:20pt\"></i> </button> </td>";
                }
                
                $envio .="</tr>";
            }
        }
    }

    echo $envio;

    pg_close();
?>