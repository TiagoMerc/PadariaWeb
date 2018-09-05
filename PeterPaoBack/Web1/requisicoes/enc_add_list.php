<?php
    include('../conectar.php');

    $busca = $_GET['busca'];
    $modo = $_GET['modo'];

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
            $envio .="<td>$obj->nome_prod</td>";
            $envio .="<td>$obj->quantidade_estoque</td>";
            $envio .="<td>$obj->categoria</td>";
            $envio .="<td>$obj->preco_unitario</td>";
            $envio .="<td><input class=\"botaoAddQtdoEnco\" type=\"number\" value=\"1\" id=\"botaoAddQtdoEnco".$obj->cod_prod."\"></td>";
            $envio .= "<td> <button class= \"plus\" type=\"plus\" onclick=\"add_prod_pedido($obj->cod_prod)\"><i class=\"fa fa-plus\" style=\"font-size:20px\"></i></button></td>";
            $envio .="</tr>";
        }
        $envio .= "</tbody></table>";
    }

    echo $envio;

    pg_close($conexao);
?>