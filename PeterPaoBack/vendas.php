<?php
    session_start();
    if(!(isset($_SESSION['log']))){
        header("Location: login.php?erro_login=2");
    }

    
    if(isset($_GET['erro_login'])){
        if($_GET['erro_login'] == 3){
            $message = "Você precisa ser Gerente para acessar a página!";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
    }
?>

<?php

    include('conectar.php');

   function mask($val, $mask) {
    $maskared = '';
    $k = 0;
    for($i = 0; $i<=strlen($mask)-1; $i++) {
        if($mask[$i] == '#') {
            if(isset($val[$k]))
                $maskared .= $val[$k++];
        }
        else {
            if(isset($mask[$i]))
                $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    if (isset($_GET["search"])) {
        $consulta = $_GET["search"];
        
        $opcao = $_GET["buscaOp"]; 
        if (strcmp($opcao, "Código") == 0) {
            $consulta = pg_escape_string($consulta);
            $query = "SELECT cod_pedido, cpf_funcio, data_venda, preco_total FROM pedido WHERE cod_pedido = $consulta ORDER BY preco_total";
        } else if (strcmp($opcao, "CPF Funcionário") == 0) {
            $consulta = str_replace(".", "", $consulta);
            $consulta = str_replace("-", "", $consulta);
            $consulta = pg_escape_string($consulta);
            $query = "SELECT cod_pedido, cpf_funcio, data_venda, preco_total FROM pedido WHERE cpf_funcio = '$consulta' ORDER BY preco_total";
        } else {
            $consulta = pg_escape_string($consulta);
            $query = "SELECT cod_pedido, cpf_funcio, data_venda, preco_total FROM pedido WHERE data_venda = '$consulta' ORDER BY preco_total";
        }
        
        $vendas = pg_query($conexao, $query);
        $numRows = pg_affected_rows($vendas);
        $rowsPerPage = 10;
        $totalPages = ceil($numRows / $rowsPerPage);

        if(isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];

            if ($page < 1) {
                $page = 1;
            } elseif ($page > $totalPages) {
                $page = $totalPages;
            }
        } else {
            $page = 1;
        }
        
        $offset = ($page - 1) * $rowsPerPage;
            
        $vendas = pg_query($conexao, $query." LIMIT ".$rowsPerPage." OFFSET ".$offset);
    
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Peter Pão: Vendas</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
       <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="css/clientes.css">
    </head>
    <body class="row">
        <div class=" col-md-3 gambi">    
           <div class="containerMenu">
            <table class="imgcontainer">
                <tr>
                    <td style="width: 30%"><img src="Img/paoIcon.png" alt="logo" class="logo"></td>
                    <td> <h3> Peter Pão </h3> </td>
                </tr>
               </table>
             <div class="vertical-menu">
                <a href="home.php" onclick="window.location.replace('home.php')"> <i class="fa fa-home" style="font-size:24px; padding-right:10px" ></i> Home </a>
                <a href="clientes.php" > <i class="fa fa-users" style="font-size:24px; padding-right:10px"></i>   Clientes </a>
                <a href="encomendas.php" onclick="window.location.replace('encomendas.php')"><i class="fa fa-book"style="font-size:24px; padding-right:10px"></i>  Encomendas</a>
                <a href="vendas.php"  class ="active" onclick="window.location.replace('vendas.php')"><i class="fa fa-shopping-basket"style="font-size:24px; padding-right:10px" ></i>  Vendas</a> 
                <a href="estoque.php" onclick="window.location.replace('estoque.php')"><i class="fa fa-archive"style="font-size:24px; padding-right:10px"></i>   Estoque</a> 
                </div>
                <?php if($_SESSION['gerente'] == 1){?>
                <div class="vertical-menuGerente">
                <a href="funcionarios.php" onclick="window.location.replace('funcionarios.php')"> <i class="fa fa-user" style="font-size:24px; padding-right:10px"></i>   Funcionários </a>
                <a href="relatorio.php"  onclick="window.location.replace('relatorio.php')"><i class="fa fa-clipboard"style="font-size:24px; padding-right:10px"></i>  Gerar Relatório</a>
                </div>
                <?php } ?>
               <table id="rodape">
                <tr>
                    <td> Nome: <?php echo $_SESSION['nome'] ?></td>

                </tr>
                <tr>
                    <td> Cargo: <?php echo $_SESSION['cargo'] ?></td>
                </tr>
                <tr>
                    <td> Data: <script language=javascript type="text/javascript">
                   now = new Date
                   document.write (now.getDate() + "/" + (now.getMonth()+1) + "/" + now.getFullYear() )
               </script></td>
                     <td style="text-align: right"> <button class="botaoSair" onclick="window.location.replace('logout.php')"><i class="fa fa-sign-out"style="font-size:40 px; padding-right:10px"></i>Sair </button></td>
                </tr>   
               </table>

              <!-- <input type="image" src="exit.png" name="exitButton" class="exitB" alt="Submit" width="20%" height="auto">-->
            </div>
        
        </div>
            <div class="col-md-9  gambi">
                <div class="container" style="background-image: url('Img/shopping-basket.png')">
                <h2>Vendas</h2>
                    <div class="conteudo">
                       <table id="menuTabela">
                            <tr>
                                <td id="consul" class="botao active"> <a href="#" onclick="change('consultar')"> Consultar</a></td>
                                <td rowspan="7" style = "width: 100%"> 
                                    <div id="consultar">
                                        <div class="busca">
                                        <form name = "consultar" method="get">  
                                          <input type="text" placeholder="por data (dd/mm/aaaa).." maxlength="10" name="search" oninput="date_change()" pattern="\d{2}/\d{2}/\d{4}" title="Apenas números. Formato final: dd/mm/aaaa" required>
                                        <select id="buscaOp" name="buscaOp" onchange="muda_buscaOp()">
                                          <option>Data</option>
                                          <option>CPF Funcionário</option>
                                          <option>Código</option>
                                        </select>
                                        </form>
                                        </div>
                                        <table id="tabelaConsultarVendas">
                                            <thead>
                                              <tr>
                                                <th>Código</th>  
                                                <th>CPF Funcionário</th>  
                                                <th>Data</th>
                                                <th>Preço Total</th> 
                                                <th>Produtos</th>  
                                              </tr>
                                            </thead>
                                            <tbody>
                                            <?php while ($ped = pg_fetch_object($vendas))
                                            {        ?>  
                                          <tr class="linhaFora">
                                            <td><?php echo $ped->cod_pedido;?></td>
                                            <td><?php 
                                             $ped->cpf_funcio = mask($ped->cpf_funcio, '###.###.###-##');
                                             echo $ped->cpf_funcio;?></td>
                                            <td><?php echo date('d/m/Y', strtotime($ped->data_venda)); ?></td>
                                            <td style="text-align:left; text-indent: 30px;"><?php echo "R$ " . money_format('%.2n', $ped->preco_total); ?></td>    
                                            <td> <button class= "prod" data-toggle="collapse" data-target="#<?php echo $ped->cod_pedido; ?>"> <i class="fa fa-chevron-circle-down"style="color:green; font-size: 15pt"></i> </button>
                                            </td> 
                                          </tr>
                                            <tr id="<?php echo $ped->cod_pedido; ?>" class="collapse">
                                                <td colspan="5"> 
                                                    <table class="produtos" >
                                                        <thead>
                                                        <tr>
                                                            <td class="cabeca"><b>Código</b></td>
                                                            <td class="cabeca" style="text-align: left"><b>Nome</b></td>
                                                            <td class="cabeca"><b>Quantidade</b></td>
                                                            
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $query = "SELECT produto.cod_prod, nome_prod, quantidade_possui FROM possui, produto WHERE possui.cod_pedido = '$ped->cod_pedido' AND possui.cod_prod = produto.cod_prod;";
                                                            $produtos = pg_query($conexao, $query);            
                                                            while($prod = pg_fetch_object($produtos)){ ?>
                                                                <tr>
                                                                    <td><?php echo $prod->cod_prod;?></td>
                                                                    <td style="text-align: left"><?php echo $prod->nome_prod;?></td>
                                                                    <td><?php echo $prod->quantidade_possui;?></td>
                                                                </tr>
                                                           <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                               
                                            </tr>  
                        
                                            <?php         
                                                }
                                                   pg_close($conexao);     
                                            ?>
                                            
                                            </tbody>
                                        </table>
<div class="index_footer">
    <?php
        if(isset($_GET['search']) && $_GET['search']!='') {
        $sideIdxAmount = 4;
        $totalIdxAmount = $sideIdxAmount * 2;

        // Limite inferior
        $idxDownLimit = ($page - $sideIdxAmount > 1 ? $page - $sideIdxAmount : 1);
        // Limite superior
        $idxUpLimit = ($page + $sideIdxAmount < $totalPages ? $page + $sideIdxAmount : $totalPages);

        // Quantidade fixa para index
        if($idxUpLimit - $idxDownLimit < $totalIdxAmount) {
            if($idxDownLimit == 1) {
                $idxUpLimit = $idxUpLimit + ($totalIdxAmount - ($idxUpLimit - $idxDownLimit));
                // Checar se passou do limite de páginas
                $idxUpLimit = ($idxUpLimit > $totalPages ? $totalPages : $idxUpLimit);
            } else {
                $idxDownLimit = $idxDownLimit - ($totalIdxAmount - ($idxUpLimit - $idxDownLimit));
            }
        }

        // Impressão de botões
        if($idxDownLimit >= 2) {
            if($page == 1) {
                ?><p class="index">1</p><?php
            } else {                         
                ?><a class="index" href="<?='?'.http_build_query(array_merge($_GET, array("page" => "1")))?>">1</a><?php
            }

            if($idxDownLimit > 2) {
                ?><p class="index" style="background: none; color: #000"><?="..."?></p><?php
            }
        }

        for($i = ($idxDownLimit); $i <= $idxUpLimit; $i++) {
            if($i == $page) {
                ?><p class="index"><?=$i?></p><?php
            } else {                         
                ?><a class="index" href="<?='?'.http_build_query(array_merge($_GET, array("page" => $i)))?>"><?=$i?></a><?php
            }
        }
        
    
        if($idxUpLimit <= $totalPages - 1) {
            if($idxUpLimit < $totalPages - 1) {
                ?><p class="index" style="background: none; color: #000"><?="..."?></p><?php
            }

            if($page == $totalPages) {
                ?><p class="index"><?=$totalPages?></p><?php
            } else {
                ?><a class="index" href="<?='?'.http_build_query(array_merge($_GET, array("page" => $totalPages)))?>"><?=$totalPages?></a><?php
            }
        }
        }
    ?>
</div>
                                    </div>
                                </td>
                           </tr>
                           <tr>
                                <td class="botao" style= "visibility: hidden;"><a></a> </td>
                               
                           </tr>
                           <tr>
                               <td class="botao"style= "visibility: hidden;"><a></a> </td>
                           </tr>
                           <tr>
                               <td class="botao" style= "visibility: hidden;"><a></a> </td>
                           </tr>
                           <tr>
                               <td class="botao"style= "visibility: hidden;"><a></a> </td>
                           </tr>
                           <tr>
                               <td class="botao"style= "visibility: hidden;"><a></a> </td>
                           </tr>
                           <tr>
                               <td class="botao"style= "visibility: hidden;"><a></a> </td>
                           </tr>
                        </table>
                    </div>
                </div>   
            </div>
    </body>
</html>

<script>
    function cpf_change(){
        var cpf = document.getElementsByName("search")[0].value;

        cpf = cpf.replace(/[\W\s\._\-]+/g, ''); //para retirar caracteres especiais
        cpf = cpf.replace(/[A-z.]+/, ''); //para retirar letras

        //vetor que recebera cada parte do cpf
        var tokens = [];

        //tamanho atual do input
        var tamanho = cpf.length;

        //retirar cada parte do cpf
        for(var i = 0; (i < tamanho) && (i < 9); i+= 3){
            tokens.push(cpf.substr(i, 3));
        }

        //processo de inserção e pontos e traços
        if(tamanho > 9){
            var que = cpf.substr(i, 2);
            cpf = tokens.join(".");
            cpf = cpf + "-" + que;
        }else{
            cpf = tokens.join(".");
        }

        //substitui no input
        document.getElementsByName("search")[0].value = cpf;
    }
    
    function date_change(){
        var date = document.getElementsByName("search")[0].value;

        date = date.replace(/[\W\s\._\-]+/g, ''); //para retirar caracteres especiais
        date = date.replace(/[A-z.]+/, ''); //para retirar letras

        //vetor que recebera cada parte do cpf
        var tokens = [];

        //tamanho atual do input
        var tamanho = date.length;

        //retirar cada parte do cpf
        for(var i = 0; (i < tamanho) && (i < 4); i+= 2){
            tokens.push(date.substr(i, 2));
        }

        //processo de inserção e pontos e traços
        if(tamanho > 4){
            tokens.push(date.substr(i, 4));
            date = tokens.join("/");
        }else{
            date = tokens.join("/");
        }

        //substitui no input
        document.getElementsByName("search")[0].value = date;
    }
    
    function muda_buscaOp(){
        var opcao = document.getElementsByName("buscaOp")[0].value;
       
        if(opcao.localeCompare("CPF Funcionário") == 0){
            $("input[name='search']").replaceWith("<input type=\"text\" placeholder=\"buscar por cpf..\" maxlength=\"14\" name=\"search\" oninput=\"cpf_change()\" pattern=\"\\d{3}.\\d{3}.\\d{3}-\\d{2}\" title=\"Apenas números. 11 no total.\" required>");
        }else if(opcao.localeCompare("Data") == 0){
            $("input[name='search']").replaceWith("<input type=\"text\" placeholder=\"por data (dd/mm/aaaa)..\" maxlength=\"10\" name=\"search\" oninput=\"date_change()\" pattern=\"\\d{2}/\\d{2}/\\d{4}\" title=\"Apenas números. Formato final: dd/mm/aaaa\" required>");
        }else if(opcao.localeCompare("Código") == 0){
            $("input[name='search']").replaceWith("<input type=\"text\" placeholder=\"buscar por código..\" maxlength=\"10\" name=\"search\" oninput=\"replace_not_number('3')\" pattern=\"\\d{1,10}\" title=\"Apenas números. 1 a 10 dígitos.\" required>");
        }
    }
    
    function replace_not_number(modo){
        if(modo == '1'){
            var cod = document.getElementsByName("search_alt")[0].value;
        }else if(modo == '2'){
            var cod = document.getElementsByName("search_add")[0].value;
        }else{
            var cod = document.getElementsByName("search")[0].value;
        }
        
            
        cod = cod.replace(/[\W\s\._\-]+/g, ''); //para retirar caracteres especiais
        cod = cod.replace(/[A-z.]+/, ''); //para retirar letras
        
        if(modo == '1'){
            document.getElementsByName("search_alt")[0].value = cod;
        }else if(modo == '2'){
            document.getElementsByName("search_add")[0].value = cod;
        }else{
            document.getElementsByName("search")[0].value = cod;
        }
    }
</script>