<?php
    include('../conectar.php');
    session_start();
    $busca = $_GET['busca'];
    $modo = $_GET['modo'];
    $mode = $_GET['forma'];

    $busca = pg_escape_string($busca);

    if($modo == '1'){ //busca por código
        $query = "SELECT cod_prod, nome_prod, quantidade_estoque, categoria, preco_unitario FROM produto WHERE cod_prod = '$busca' ORDER BY cod_prod;";
    }else if($modo == '2'){//busca por nome
        $query = "SELECT cod_prod, nome_prod, quantidade_estoque, categoria, preco_unitario FROM produto WHERE nome_prod ILIKE '%$busca%'  ORDER BY cod_prod;";
    }else{ //busca por categoria
        $query = "SELECT cod_prod, nome_prod, quantidade_estoque, categoria, preco_unitario FROM produto WHERE categoria = '$busca' ORDER BY cod_prod;";
    }

    $result = pg_query($conexao, $query);
    $nlinhas = pg_affected_rows($result);

    if($nlinhas == 0){
        $envio = "<p>&#8195;Sua busca não retornou resultados!</p>";
    }else{
        $envio = "<table id=\"tabelaConsultar\">     
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Categoria</th>  
                        <th>Preço Unitário</th>
                        <th>Quantidade a ser<br> Adicionada</th>  
                        <th>Add</th>  
                     </tr>
                    </thead> <tbody>";
        
        while($obj = pg_fetch_object($result)){
            $envio .="<tr>";
            $envio .="<td>$obj->cod_prod</td>";
            $envio .="<td style='text-align:left; padding-left:10px'>$obj->nome_prod</td>";
            $envio .="<td>$obj->quantidade_estoque</td>";
            $envio .="<td style='text-align:left; padding-left:10px'>$obj->categoria</td>";
            $envio .="<td style='text-align:left; padding-left:10px'> R$ ". money_format('%.2n', $obj->preco_unitario). "</td>";
            if($obj->quantidade_estoque == 0){
                $envio .="<td> Sem estoque.</td>";
                $envio .= "<td> </td>";
            }else{
                if($mode == '1'){
                    $envio .="<td><input class=\"botaoAddQtdoEnco\" type=\"number\" oninput=\"limit_por_estoque($obj->cod_prod, $obj->quantidade_estoque, '1')\" value=\"1\" id=\"botaoAddQtdoEnco".$obj->cod_prod."\"></td>";
                    
                    $envio .= "<td> <button class= \"plus\" type=\"plus\" onclick=\"add_prod_pedido($obj->cod_prod, '1')\"><i class=\"fa fa-plus\" style=\"font-size:20px\"></i></button></td>";
                }else{
                    $envio .="<td><input class=\"botaoAddQtdoEnco\" type=\"number\" oninput=\"limit_por_estoque($obj->cod_prod, $obj->quantidade_estoque, '2')\" value=\"1\" id=\"botaoAddQtdoEncoAlt".$obj->cod_prod."\"></td>";
                    $envio .= "<td> <button class= \"plus\" type=\"plus\" onclick=\"add_prod_pedido($obj->cod_prod, '2')\"><i class=\"fa fa-plus\" style=\"font-size:20px\"></i></button></td>";
                }
            }
            $envio .="</tr>";
        }
        $envio .= "</tbody></table>";
    }

    echo $envio;

    pg_close($conexao);
?>