<?php
    session_start();
    if(isset($_COOKIE['value_p'])){
        $_SESSION['cod_pedido'] = $_COOKIE['value_p'];
        setcookie('value_p', '', 1);
        header("Location: encomendas.php?setpage=pedido");
    }

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


    include('conectar.php');

    if (isset($_POST["search"])) {
        $consulta = $_POST["search"];
        $opcao = $_POST["buscaOp"]; 
        if (strcmp($opcao, "Código") == 0) {
            $consulta = pg_escape_string($consulta);
            $query = "SELECT * FROM encomendas_cod_pedido('$consulta')";
        } else if (strcmp($opcao, "CPF Cliente") == 0) {
            $consulta = str_replace(".", "", $consulta);
            $consulta = str_replace("-", "", $consulta);
            $consulta = pg_escape_string($consulta);
            $query = "SELECT * FROM encomendas_cpf_cliente('$consulta')";
        } else {
            $consulta = pg_escape_string($consulta);
            $query = "SELECT * FROM encomendas_data_entrega('$consulta')";
        }   
        
        $result = pg_query($conexao, $query);
    
    }

?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Peter Pão: Encomendas</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
       <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/table.css">
        <link rel="stylesheet" href="css/clientes.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <script>
            var cod_ped_alt = -1;
            window.onload = function(){
                n = window.location.href.search("setpage=pedido");
                if (n > -1) {
                    change("adicionar");
                }
                n = window.location.href.search("setpage=consul");
                if (n > -1) {
                    change("consultar");
                }
            }
        </script>
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
                <a href="clientes.php" onclick="window.location.replace('clientes.php')"> <i class="fa fa-users" style="font-size:24px; padding-right:10px"></i>   Clientes </a>
                <a href="encomendas.php" class="active"  onclick="window.location.replace('encomendas.php')"><i class="fa fa-book"style="font-size:24px; padding-right:10px"></i>  Encomendas</a>
                <a href="vendas.php" onclick="window.location.replace('vendas.php')"><i class="fa fa-shopping-basket"style="font-size:24px; padding-right:10px"></i>  Vendas </a> 
                <a href="estoque.php" onclick="window.location.replace('estoque.php')"><i class="fa fa-archive"style="font-size:24px; padding-right:10px"></i>   Estoque</a> 
                </div>
                <?php if($_SESSION['gerente'] == 1){?>
                <div class="vertical-menuGerente">
                <a href="funcionarios.php'" onclick="window.location.replace('funcionarios.php')"> <i class="fa fa-user" style="font-size:24px; padding-right:10px"></i>   Funcionários </a>
                <a href="relatorio.php"  onclick="window.location.replace('relatorio.php')"><i class="fa fa-clipboard"style="font-size:24px; padding-right:10px"></i>  Gerar Relatório</a>
                </div>
                <?php } ?>
               <table id="rodape">
                <tr>
                    <td> Nome: <?php echo $_SESSION['nome']; ?></td>

                </tr>
                <tr>
                    <td> Cargo: <?php echo $_SESSION['cargo']; ?></td>
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
        <script>
            function change(x){
               if(x === "consultar"){
                   document.getElementById("consultar").style.display = 'block';
                   document.getElementById("adicionar").style.display = 'none';
                   document.getElementById("alterar").style.display = 'none';
                   document.getElementById("consultarProd").style.display = 'none';
                   document.getElementById("consul").className = "botao active";
                   document.getElementById("add").className = "botao";
                   document.getElementById("consultarProd1").style.display = 'none';
                   
                    n = window.location.href.search("setpage=pedido");
                    if (n > -1) {
                        location.href="encomendas.php";
                    }
                   
                }else if(x === "adicionar"){
                    document.getElementById("adicionar").style.display = 'block';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("alterar").style.display = 'none';
                    document.getElementById("consultarProd").style.display = 'none';
                    document.getElementById("consul").className = "botao";
                    document.getElementById("add").className = "botao active";
                    document.getElementById("consultarProd1").style.display = 'none';
                    list_pedido('1');
                }else if(x === "alterar"){
                    document.getElementById("alterar").style.display = 'block';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("consultarProd").style.display = 'none';
                    document.getElementById("adicionar").style.display = 'none';
                    document.getElementById("consultarProd1").style.display = 'none';
                    list_pedido('2');
                }else if(x === "addProd"){
                    document.getElementById("alterar").style.display = 'none';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("consultarProd").style.display = 'block';
                    document.getElementById("adicionar").style.display = 'none';
                    document.getElementById("consultarProd1").style.display = 'none';
                }else if(x === "addProdEdit"){
                    document.getElementById("alterar").style.display = 'none';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("consultarProd").style.display = 'none';
                    document.getElementById("adicionar").style.display = 'none';
                    document.getElementById("consultarProd1").style.display = 'block';
                }
                
            }
            </script>
            <script>
                flag = 0;
                function changeText(pedido) {
                    if(flag === 0){
                     document.getElementById('pago').innerHTML = 'Sim';
                         
                        flag = 1;

                    }else{
                        document.getElementById('pago').innerHTML = 'Não';
                        flag =0;
                    }
                }
                </script>
        </div>
            <div class="col-md-9  gambi">
                <div class="container" style="background-image: url('Img/groceries.png')">
                <h2>Encomendas</h2>
                    <div class="conteudo">
                       <table id="menuTabela">
                            <tr>
                                <td id="consul" class="botao active"> <a href="#" onclick="change('consultar')"> Consultar</a></td>
                                <td rowspan="7" style = "width: 100%"> 
                                    <div id="consultar">
                                        <div class="busca">
                                        <form name = "consultar" method="post">    
                                          <input type="text" placeholder="buscar por código.." maxlength="10" name="search" oninput="replace_not_number('3')" pattern="\d{1,10}" title="Apenas números" required>
                                          <select id="buscaOp" name="buscaOp" onchange="muda_buscaOp()">
                                          <option>Código</option>
                                          <option>Data de Entrega</option>
                                          <option>CPF Cliente</option>
                                        </select>
                                            <input type="hidden" name="update" value="<?php echo $editarValores;?>">
                                        </form>    
                                        </div>
                                        <table id="tabelaConsultarEncomendas">
                                            <thead>
                                          <tr class="linhaFora">
                                            <th>Código</th>
                                            <th>Cliente</th>
                                            <th>CPF</th>  
                                            <th>Data de Entrega</th>
                                            <th>Preço Total</th>
                                            <th>Produtos</th>
                                            <th>Editar</th>
                                            <th>Remover</th>
                                          </tr>
                                            </thead>
                                        <tbody>
                                            
                                             <?php
                                                 while($obj = pg_fetch_object($result))
                                                {        ?>
                                                <tr class="linhaFora">
                                                <td><?php echo $obj->cod_pedido;?></td>
                                                <td style="text-align:left; padding-left: 30px;"><?php echo $obj->nome_cliente;?></td>    
                                                <td><?php
                                                 $obj->cpf_cliente = mask($obj->cpf_cliente, '###.###.###-##');
                                                 echo $obj->cpf_cliente; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($obj->data_entrega)); ?></td>
                                                <td style="text-align:left; padding-left: 15px;"><?php echo "R$ " . money_format('%.2n', $obj->preco_total); ?></td>     
                                                
                                                <td> 
                                                    <button class= "prod" data-toggle="collapse" data-target="#<?php echo $obj->cod_pedido; ?>"> <i class="fa fa-chevron-circle-down"style="color:green; font-size: 15pt"></i> </button>
                                                </td>   
                                                <td> 
                                                    <button class= "edit" type="edit" <?php if($obj->pago == '1'){ echo "onclick=\"aviso_alt()\""; }else{ echo "onclick=\"cod_ped_alt = $obj->cod_pedido; list_pedido('2'); get_date_enc(); change('alterar');\""; } ?>> <i class="material-icons" style="color:white; font-size: 10pt">edit</i> </button>
                                                </td>
                                                    <td> <button class= "cancelarEnco" type="edit" <?php if($obj->pago == '1'){ echo "onclick=\"aviso_alt()\""; }else{ echo "onclick=\"cod_ped_alt = $obj->cod_pedido; cancela_ped('1'); \""; } ?>> <i class="fa fa-close" style="color:white; font-size: 10pt"></i> </button> </td>
                                                </tr>
                                            
                                            <tr id="<?php echo $obj->cod_pedido; ?>" class="collapse">
                                              <td colspan="8"> 
                                                <table class="produtosEncomeda" colspan="5">
                                                   
                                                    <tr> 
                                                        <td style="text-align: center"><b>Data do Pedido: </b><?php echo date('d/m/Y', strtotime($obj->data_pedido)); ?></td>
                                                        <td style="text-align: center" colspan="2">Pago:
                                                        <?php
                                                            if($obj->pago == '1') {
                                                                echo "Sim";
                                                            } else {
                                                                echo "Não";
                                                            }
                                                        ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Código</b></td>
                                                        <td style="text-align: left"><b>Nome</b></td>
                                                        <td><b>Quantidade</b></td>
                                                    </tr>
                                                    <?php
                                                    $query = "SELECT produto.cod_prod, nome_prod, quantidade_possui FROM possui, produto WHERE possui.cod_pedido = '$obj->cod_pedido' AND possui.cod_prod = produto.cod_prod;";
                                                    $produtos = pg_query($conexao, $query);
                                                        
                                                        while($prod = pg_fetch_object($produtos)){ ?>
                                                            <tr>
                                                                <td><?php echo $prod->cod_prod;?></td>
                                                                <td style="text-align: left"><?php echo $prod->nome_prod;?></td>
                                                                <td><?php echo $prod->quantidade_possui;?></td>
                                                            </tr>    
                                                       <?php } ?>
                                                </table>
                                                </td>
                                            </tr>
                                          <?php         
                                                }
                                               pg_close($conexao);      
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="adicionar">
                                        <form onsubmit="tenta_confirmar(); return false;">
                                             <div class="containerBox">
                                                 <table id="formulario">
                                                    <tr>
                                                     <td rowspan="2" colspan="2"> 
                                                         <label for="produtos" class="titulo"><b>    Produtos </b></label>
                                                         <table id ="listaPed" class="prodConsul">     
                                                         </table></td>
                                                        <td><button type="addProd" onclick="change('addProd')">Adicionar Produtos <i class="fa fa-cart-plus" style="font-size:20pt"></i></button></td>
                                                     </tr>
                                                     <tr> 
                                                        <td> <p id="preco_total_ped">Valor Total: R$ 0,00</p></td>
                                                     </tr>
                                                     <tr>
                                                         <td > <label for="cpf" class="titulo"><b>    CPF </b></label>
                                                        <input type="text"  id="cpf" oninput="cpf_replace(this.form.cpf.value,'cpf')" placeholder="xxx.xxx.xxx-xx" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="CPF tem 11 dígitos (Digite apenas números)" maxlenght="15" required autofocus ></td>
                                                        <td> <label for="dataEntrega" class="titulo"><b>    Data de Entrega </b></label>
                                                        <input type="date" id="data_enc" placeholder="dd/mm/aaaa" name="dataEntre" required >
                                                         </td>
                                                         <td> <label for="pago" class="titulo"><b>   Pago </b></label>
                                                        <select id="setCargo" name="setPago">
                                                          <option value="0">Não</option>
                                                          <option value="1">Sim</option>
                                                        </select>
                                                         </td>
                                                     </tr>
                                                 </table>
                                              </div>
                                            <p id="erros_input" class="aviso_enc"></p>
                                            <div class="botoes" style=" margin: 7% 0 0 0;">
                                                <button type="reset" onclick="cancela_ped('2')" class="cancelbtn">Limpar</button>
                                                <button type="submit">Confirmar</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div id="alterar">
                                        <h3>Alterar Dados</h3>
                                        <form onsubmit="return false;">
                                             <div class="containerBox">
                                                 <table id="formulario">
                                                    <tr>
                                                     <td rowspan="2" colspan="2"> 
                                                         <label for="produtos" class="titulo"><b>    Produtos </b></label>
                                                         <table id="lista_ped_alt" class="prodConsul">   
                                                         </table></td>
                                                        <td><button type="addProd" onclick="change('addProdEdit')">Adicionar Produtos <i class="fa fa-cart-plus" style="font-size:20pt"></i></button></td>
                                                     </tr>
                                                     <tr> 
                                                        <td> <p id="valor_ped_alt">Valor Total: R$ 0,00</p></td>
                                                     </tr>
                                                     <tr>
                                                        <td> <label for="dataEntrega" class="titulo"><b>    Data de Entrega </b></label>
                                                        <input type="date" id="data_alt" placeholder="dd/mm/aaaa" name="dataEntreAlt" required autofocus>
                                                         </td>
                                                         <td> <label for="pago" class="titulo"><b>   Pago </b></label>
                                                        <select id="setCargo" name="setPago_ped_alt">
                                                          <option value="0">Não</option>
                                                          <option value="1">Sim</option>
                                                        </select>
                                                         </td>
                                                     </tr>
                                                 </table>
                                              </div>
                                            <p id="erros_input_2" class="aviso_enc"></p>
                                            <div class="botoes" style=" margin: 7% 0 0 0;">
                                                <button type="voltar" class="voltar" onclick="change('consultar')"><i class="material-icons" style="color:white; font-size: 30pt">keyboard_return</i></button>
                                                <button type="submit" onclick="tenta_alterar()">Confirmar</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div id="consultarProd">
                                        <h3>Adicionar Produtos na Encomenda</h3>
                                        <div class="busca">
                                          <input type="text" id="consulta_add" placeholder="buscar por código.." maxlength="10" name="search_add" oninput="replace_not_number()" onchange="consulta_add('1')">
                                          <select id="buscaOp" name="buscaOp_add" onchange="muda_busca(2); consulta_add('1')">
                                          <option value="1">Código</option>
                                          <option value="2">Nome</option>
                                          <option value="3">Categoria</option>
                                        </select>
                                        </div>
                                        <div id="tabela_result_add">
                                            <p>&#8195;Para buscar selecione o modo de pesquisa, digite e pressione Enter.</p>
                                        </div>
                                        <div class="botoes" style=" margin: 7% 0 0 0;">
                                                <button type="button" class="voltar" onclick="change('adicionar')"><i class="material-icons" style="color:white; font-size: 30pt">keyboard_return</i></button>
                                        </div>
                                    </div>
                                    <div id="consultarProd1">
                                        <h3>Adicionar Novos Produtos na Encomenda</h3>
                                        <div class="busca">
                                          <input type="text" id="consulta_alt" placeholder="buscar por código.." maxlength="10" name="search_alt" oninput="replace_not_number('1')" onchange="consulta_add('2')">
                                          <select id="buscaOp" name="buscaOp_alt" onchange="muda_busca('1'); consulta_add('2')">
                                          <option value="1">Código</option>
                                          <option value="2">Nome</option>
                                          <option value="3">Categoria</option>
                                        </select>
                                        </div>
                                        <div id="tabela_result_alt">
                                            <p>&#8195;Para buscar selecione o modo de pesquisa, digite e pressione Enter.</p>
                                        </div>
                                        <div class="botoes" style=" margin: 7% 0 0 0;">
                                                <button type="button" class="voltar" onclick="change('alterar')"><i class="material-icons" style="color:white; font-size: 30pt">keyboard_return</i></button>
                                        </div>
                                    </div>
                                </td>
                           
                           <tr>
                                <td id="add" class="botao"> <a href="#" onclick="change('adicionar')"> Adicionar</a></td>
                           </tr>
                           <tr>
                               <td class="botao"  style= "visibility: hidden;"><a></a></td>
                           </tr>
                           <tr>
                               <td class="botao"style= "visibility: hidden;"><a></a></td>
                           </tr>
                           <tr>
                               <td class="botao"style= "visibility: hidden;"><a></a></td>
                           </tr>
                           <tr>
                               <td class="botao"style= "visibility: hidden;"><a></a></td>
                           </tr>
                           <tr>
                               <td class="botao"style= "visibility: hidden;"><a></a></td>
                           </tr>
                        </table>
                    </div>
                </div>   
            </div>
    </body>
<? php
 pg_close($conexao);      
?>
</html>

<script>
    function cpf_replace(pCpf, id_form){
        var cpf = pCpf.toString()

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
        document.getElementById(id_form).value = cpf
    }
</script>

<script language="javascript" type="text/javascript">
    //Função AJAX para add no estoque -->
    function consulta_add(mode){
        var requisicao;

        try{
            requisicao = new XMLHttpRequest();
        }catch(e){
            try{
                requisicao = new ActiveXObject("Msxml2.XMLHTTP");
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                }catch(e){
                    alert("Navegador incompatível com AJAX");
                    return false;
                }
            }
        }

        requisicao.onreadystatechange = function(){
            if(mode == '1'){
                if(requisicao.readyState == 2){
                    var tabela = document.getElementById('tabela_result_add');
                    tabela.innerHTML = "<p>&#8195;Realizando a busca...</p>";
                }else if(requisicao.readyState == 4){
                    document.getElementById("erros_input").innerHTML = "";
                    var tabela = document.getElementById('tabela_result_add');
                    tabela.innerHTML = requisicao.responseText;
                }
            }else{
                if(requisicao.readyState == 2){
                    var tabela = document.getElementById('tabela_result_alt');
                    tabela.innerHTML = "<p>&#8195;Realizando a busca...</p>";
                }else if(requisicao.readyState == 4){
                    document.getElementById("erros_input_2").innerHTML = "";
                    var tabela = document.getElementById('tabela_result_alt');
                    tabela.innerHTML = requisicao.responseText;
                }
            }
        }

        var get_atrib;
        if(mode == '1'){
            var busca = document.getElementsByName('search_add')[0].value;
            var modo = document.getElementsByName('buscaOp_add')[0].value;

            get_atrib = "?busca=" + busca + "&modo=" + modo + "&forma=1";
        }else{
            var busca = document.getElementsByName('search_alt')[0].value;
            var modo = document.getElementsByName('buscaOp_alt')[0].value;

            get_atrib = "?busca=" + busca + "&modo=" + modo + "&forma=2";
        }
        
        requisicao.open("GET", "requisicoes/enc_add_list.php" + get_atrib, true);
        requisicao.send();
    }
    
    
    
    //Função de adição e criação de pedido
    function add_prod_pedido(cod_prod, modo){
        if(modo == '1'){
           var cod_ped = <?php echo $_SESSION['cod_pedido'];?>;
        }else{
           var cod_ped = cod_ped_alt;
        }
        
        //caso não tenha pedidos
        if(cod_ped == -1){
            var requisicao;

            try{
                requisicao = new XMLHttpRequest();
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Msxml2.XMLHTTP");
                }catch(e){
                    try{
                        requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                    }catch(e){
                        alert("Navegador incompatível com AJAX");
                        return false;
                    }
                }
            }

            //cria pedido e registra o numero na sessao
            requisicao.onreadystatechange = function(){
                if(requisicao.readyState == 4){
                    cod_ped = requisicao.responseText;
                    var d = new Date();
                    d.setTime(d.getTime() + (30*24*60*60*1000));
                    var expires = "expires="+ d.toUTCString();
                    document.cookie = "value_p=" + requisicao.responseText + ";" + expires+ ";";
                   
                    var reqPed;

                    try{
                        reqPed = new XMLHttpRequest();
                    }catch(e){
                        try{
                            reqPed = new ActiveXObject("Msxml2.XMLHTTP");
                        }catch(e){
                            try{
                                reqPed = new ActiveXObject("Microsoft.XMLHTTP");
                            }catch(e){
                                alert("Navegador incompatível com AJAX");
                                return false;
                            }
                        }
                    }

                    reqPed.onreadystatechange = function(){
                        if(reqPed.readyState == 4){
                            alert("Pedido criado com sucesso!");
                            consulta_add('1');
                            list_pedido('1');
                            location.reload();
                        }
                    }

                    //
                    var quantidade = document.getElementById("botaoAddQtdoEnco" + cod_prod).value;
                    var get_atrib = "?cod=" + cod_prod + "&quant=" + quantidade + "&pedido=" + cod_ped;

                    reqPed.open("GET", "requisicoes/enc_add_pedido.php" + get_atrib, true);
                    reqPed.send();
                }
            }

            var get_atrib = "?cpf=" + <?php echo $_SESSION['cpf']; ?>;
            requisicao.open("GET", "requisicoes/enc_create_ped.php" + get_atrib, true);
            requisicao.send();
        }else{ //caso ja tenha pedido
            //começa a adicionar produtos
            var requisicao;

            try{
                requisicao = new XMLHttpRequest();
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Msxml2.XMLHTTP");
                }catch(e){
                    try{
                        requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                    }catch(e){
                        alert("Navegador incompatível com AJAX");
                        return false;
                    }
                }
            }

            //adiciona com relação ao pedido criado
            requisicao.onreadystatechange = function(){
                if(requisicao.readyState == 4){
                    if(modo == '1'){
                        alert(requisicao.responseText);
                        consulta_add('1');
                        list_pedido('1');
                    }else{
                        alert(requisicao.responseText);
                        consulta_add('2');
                        list_pedido('2');
                    }
                }
            }

            var get_atrib;
            if(modo == '1'){
                var quantidade = document.getElementById("botaoAddQtdoEnco" + cod_prod).value;
                get_atrib = "?cod=" + cod_prod + "&quant=" + quantidade + "&pedido=" + cod_ped + "&modo=1";
            }else{
                var quantidade = document.getElementById("botaoAddQtdoEncoAlt" + cod_prod).value;
                get_atrib = "?cod=" + cod_prod + "&quant=" + quantidade + "&pedido=" + cod_ped + "&modo=2";
            }
            
            requisicao.open("GET", "requisicoes/enc_add_pedido.php" + get_atrib, true);
            requisicao.send();
        }
    }
    
   
    function list_pedido(modo){
        var requisicao;

        try{
            requisicao = new XMLHttpRequest();
        }catch(e){
            try{
                requisicao = new ActiveXObject("Msxml2.XMLHTTP");
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                }catch(e){
                    alert("Navegador incompatível com AJAX");
                    return false;
                }
            }
        }
        
        //adiciona com relação ao pedido criado
        requisicao.onreadystatechange = function(){
            if(requisicao.readyState == 4){
                if(modo == '1'){
                    document.getElementById("listaPed").innerHTML = requisicao.responseText;
                    get_preco_total('1');
                }else{
                    document.getElementById("lista_ped_alt").innerHTML = requisicao.responseText;
                    get_preco_total('2');
                }
                
            }
        }
        
        var get_atrib;
        if(modo == '1'){
            get_atrib = "?pedido=" + <?php echo $_SESSION['cod_pedido']; ?> + "&modo=1";
        }else{
            get_atrib = "?pedido=" + cod_ped_alt + "&modo=2";
        }

        requisicao.open("GET", "requisicoes/enc_list_pedido.php" + get_atrib, true);
        requisicao.send();
    }
    
    function limit_por_estoque(cod_prod, estoque, modo){
        var valor;
        
        if(modo == '1'){
            valor = document.getElementById("botaoAddQtdoEnco" + cod_prod).value;         
            if(valor < 0){
                document.getElementById("botaoAddQtdoEnco" + cod_prod).value = 0;
            }
        }else{
            valor = document.getElementById("botaoAddQtdoEncoAlt" + cod_prod).value;
            if(valor < 0){
                document.getElementById("botaoAddQtdoEncoAlt" + cod_prod).value = 0;
            }
        }
        
    }
    
    function get_preco_total(modo){
        var requisicao;

        try{
            requisicao = new XMLHttpRequest();
        }catch(e){
            try{
                requisicao = new ActiveXObject("Msxml2.XMLHTTP");
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                }catch(e){
                    alert("Navegador incompatível com AJAX");
                    return false;
                }
            }
        }
        
        //adiciona com relação ao pedido criado
        requisicao.onreadystatechange = function(){
            if(requisicao.readyState == 4){
                if(modo == '1'){
                    document.getElementById("preco_total_ped").innerHTML = requisicao.responseText;
                }else{
                    document.getElementById("valor_ped_alt").innerHTML = requisicao.responseText;
                }
            }
        }
        
        var get_atrib;
        if(modo == '1'){
            get_atrib = "?pedido=" + <?php echo $_SESSION['cod_pedido']; ?>;
        }else{
            get_atrib = "?pedido=" + cod_ped_alt;
        }
        
        requisicao.open("GET", "requisicoes/enc_valor_ped.php" + get_atrib, true);
        requisicao.send();
        
        
    }
    
    
    
    function update_quanti(cod_prod, modo){
        var requisicao;
        try{
            requisicao = new XMLHttpRequest();
        }catch(e){
            try{
                requisicao = new ActiveXObject("Msxml2.XMLHTTP");
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                }catch(e){
                    alert("Navegador incompatível com AJAX");
                    return false;
                }
            }
        }
        
        //adiciona com relação ao pedido criado
        requisicao.onreadystatechange = function(){
            if(requisicao.readyState == 4){
                if(modo == '1'){
                    document.getElementById("quantList" + cod_prod).innerHTML = requisicao.responseText;
                    list_pedido('1');
                    consulta_add('1');
                }else{
                    document.getElementById("quantListAlt" + cod_prod).innerHTML = requisicao.responseText;
                    list_pedido('2');
                    consulta_add('2');
                }
            }
        }
        
        var get_atrib;
        var quanti_up;
        if(modo == '1'){
            quanti_up = document.getElementById("quantList"+cod_prod).value;
            get_atrib = "?pedido=" + <?php echo $_SESSION['cod_pedido']; ?> + "&prod=" + cod_prod + "&quant=" + quanti_up + "&modo=1&forma=1";
        }else{
            quanti_up = document.getElementById("quantListAlt"+cod_prod).value;
            get_atrib = "?pedido=" + cod_ped_alt+ "&prod=" + cod_prod + "&quant=" + quanti_up + "&modo=1&forma=2";
        }

        requisicao.open("GET", "requisicoes/enc_list_alter_remov.php" + get_atrib, true);
        requisicao.send();
    }
    
    
    
    function delete_prod(cod_prod, modo){
        var requisicao;

        try{
            requisicao = new XMLHttpRequest();
        }catch(e){
            try{
                requisicao = new ActiveXObject("Msxml2.XMLHTTP");
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                }catch(e){
                    alert("Navegador incompatível com AJAX");
                    return false;
                }
            }
        }
        
        //adiciona com relação ao pedido criado
        requisicao.onreadystatechange = function(){
            if(requisicao.readyState == 4){
                if(modo == '2'){
                    list_pedido('2');
                    consulta_add('2');
                }else{
                    list_pedido('1');
                    consulta_add('1');
                }
            }
        }
        
        var get_atrib;
        if(modo == '1'){
            get_atrib = "?pedido=" + <?php echo $_SESSION['cod_pedido']; ?> + "&prod=" + cod_prod + "&modo=2";
        }else{
            get_atrib = "?pedido=" + cod_ped_alt + "&prod=" + cod_prod + "&modo=2";
        }

        requisicao.open("GET", "requisicoes/enc_list_alter_remov.php" + get_atrib, true);
        requisicao.send();
    }
    
    function cancela_ped(modo){
        if(modo == '1'){
            var requisicao;

            try{
                requisicao = new XMLHttpRequest();
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Msxml2.XMLHTTP");
                }catch(e){
                    try{
                        requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                    }catch(e){
                        alert("Navegador incompatível com AJAX");
                        return false;
                    }
                }
            }

            //adiciona com relação ao pedido criado
            requisicao.onreadystatechange = function(){
                if(requisicao.readyState == 4){
                    var resp = requisicao.responseText;
                    if(resp == 1){
                        alert("Pedido excluído com sucesso!");
                    }else{
                        alert("Pedido não foi excluído!");
                    }
                    location.href="encomendas.php";
                }
            }

            get_atrib = "?pedido=" + cod_ped_alt;
            
            requisicao.open("GET", "requisicoes/enc_remov_ped.php" + get_atrib, true);
            requisicao.send();
        }else{
            var requisicao;

            try{
                requisicao = new XMLHttpRequest();
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Msxml2.XMLHTTP");
                }catch(e){
                    try{
                        requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                    }catch(e){
                        alert("Navegador incompatível com AJAX");
                        return false;
                    }
                }
            }

            //adiciona com relação ao pedido criado
            requisicao.onreadystatechange = function(){
                if(requisicao.readyState == 4){
                    var resp = requisicao.responseText;
                    if(resp == 1){
                        alert("Pedido excluído com sucesso!");
                    }else{
                        alert("Pedido não foi excluído!");
                    }
                    location.href="encomendas.php?setpage=pedido";
                }
            }

            requisicao.open("GET", "requisicoes/enc_remov_ped.php", true);
            requisicao.send();
        }
    }
    
    function tenta_confirmar(){
        var cpf_cli = document.getElementById("cpf").value;
        var data = document.getElementById("data_enc").value;
        var pago = document.getElementsByName("setPago")[0].value;
        var cod_ped = <?php echo $_SESSION['cod_pedido'];?>;
        
        if(cod_ped == -1){
           alert("Não há pedido criado ainda!");
        }else{
           var requisicao;

            try{
                requisicao = new XMLHttpRequest();
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Msxml2.XMLHTTP");
                }catch(e){
                    try{
                        requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                    }catch(e){
                        alert("Navegador incompatível com AJAX");
                        return false;
                    }
                }
            }

            //adiciona com relação ao pedido criado
            requisicao.onreadystatechange = function(){
                if(requisicao.readyState == 4){
                    if(requisicao.responseText == -1){
                        document.getElementById("erros_input").innerHTML = "CPF não está registrado";
                    }else if(requisicao.responseText == -2){
                        document.getElementById("erros_input").innerHTML = "Encomendas devem possuir ao menos um produto!";
                    }else if(requisicao.responseText == -3){
                        document.getElementById("erros_input").innerHTML = "Cliente é infrator!";
                    }else{
                        document.getElementById("erros_input").innerHTML = "";
                        alert("Encomenda nº" + requisicao.responseText + " Registrada com sucesso");
                        location.href="encomendas.php";
                    }
                }
            }

            var get_atrib = "?cliente=" + cpf_cli + "&data=" + data + "&pago=" + pago;

            requisicao.open("GET", "requisicoes/enc_confirm_ped.php" + get_atrib, true);
            requisicao.send();   
        }
             
    }
    
    function muda_busca(modo){
        if(modo == 1){
            var opcao = document.getElementsByName("buscaOp_alt")[0].value;
            switch(opcao){
                case '1': 
                    $("input[name='search_alt']").replaceWith("<input type=\"text\" id=\"consulta_add\" placeholder=\"buscar por código..\" maxlength=\"10\" name=\"search_alt\" oninput=\"replace_not_number('1')\" onchange=\"consulta_add('2')\">");
                    $("select[name='search_alt']").replaceWith("<input type=\"text\" id=\"consulta_add\" placeholder=\"buscar por código..\" maxlength=\"10\" name=\"search_alt\" oninput=\"replace_not_number('1')\" onchange=\"consulta_add('2')\">");
                    break;

                case '2':
                    $("select[name='search_alt']").replaceWith("<input type=\"text\" id=\"consulta_add\" placeholder=\"buscar por nome..\" maxlength=\"50\" name=\"search_alt\" onchange=\"consulta_add('2')\">");
                    $("input[name='search_alt']").replaceWith("<input type=\"text\" id=\"consulta_add\" placeholder=\"buscar por nome..\" maxlength=\"50\" name=\"search_alt\" onchange=\"consulta_add('2')\">");
                    break;

                case '3':
                    $("input[name='search_alt']").replaceWith( "<select name= \"search_alt\" onchange=\"consulta_add('2')\" id=\"buscaOp\" onchange=\"consulta_add('1')\"><option value=\"Pães\">Pães</option> <option value=\"Tortas\">Tortas</option> <option value=\"Bolos\">Bolos</option> <option value=\"Doces\">Doces</option> <option value=\"Bebidas\">Bebidas</option> <option value=\"Ingrediente\">Ingrediente</option></select> ");
                    break;

                default:
                   alert("Erro inesperado");
            }
        }else{
            var opcao = document.getElementsByName("buscaOp_add")[0].value;
            switch(opcao){
                case '1': 
                    $("input[name='search_add']").replaceWith("<input type=\"text\" id=\"consulta_add\" placeholder=\"buscar por código..\" maxlength=\"10\" name=\"search_add\" oninput=\"replace_not_number('2')\" onchange=\"consulta_add('1')\">");
                    $("select[name='search_add']").replaceWith("<input type=\"text\" id=\"consulta_add\" placeholder=\"buscar por código..\" maxlength=\"10\" name=\"search_add\" oninput=\"replace_not_number('2')\" onchange=\"consulta_add('1')\">");
                    break;

                case '2':
                    $("select[name='search_add']").replaceWith("<input type=\"text\" id=\"consulta_add\" placeholder=\"buscar por nome..\" maxlength=\"50\" name=\"search_add\" onchange=\"consulta_add('1')\">");
                    $("input[name='search_add']").replaceWith("<input type=\"text\" id=\"consulta_add\" placeholder=\"buscar por nome..\" maxlength=\"50\" name=\"search_add\" onchange=\"consulta_add('1')\">");
                    break;

                case '3':
                    $("input[name='search_add']").replaceWith( "<select name= \"search_add\" onchange=\"consulta_add('1')\" id=\"buscaOp\" onchange=\"consulta_add('1')\"><option value=\"Pães\">Pães</option> <option value=\"Tortas\">Tortas</option> <option value=\"Bolos\">Bolos</option> <option value=\"Doces\">Doces</option> <option value=\"Bebidas\">Bebidas</option> <option value=\"Ingrediente\">Ingrediente</option></select> ");
                    break;

                default:
                   alert("Erro inesperado");
            }
        } 
    }
    
    
    function aviso_alt(){
        alert("Pedido Pago! Não é possível alterar ou remover.");
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
    
    function get_date_enc(){   
        var requisicao;
        
        try{
            requisicao = new XMLHttpRequest();
        }catch(e){
            try{
                requisicao = new ActiveXObject("Msxml2.XMLHTTP");
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                }catch(e){
                    alert("Navegador incompatível com AJAX");
                    return false;
                }
            }
        }

        //adiciona com relação ao pedido criado
        requisicao.onreadystatechange = function(){
            if(requisicao.readyState == 4){
                if(requisicao.responseText == '-1'){
                    alert("Houve um erro ao receber a data da encomenda.");
                    location.href="encomendas.php";
                }else{
                    document.getElementsByName("dataEntreAlt")[0].value = requisicao.responseText;
                }
            }
        }

        var get_atrib = "?pedido=" + cod_ped_alt;

        requisicao.open("GET", "requisicoes/enc_date.php" + get_atrib, true);
        requisicao.send(); 
    }
    
    function tenta_alterar(){
        var data_enc = document.getElementsByName("dataEntreAlt")[0].value;
        var pago = document.getElementsByName("setPago_ped_alt")[0].value;
        var requisicao;

        try{
            requisicao = new XMLHttpRequest();
        }catch(e){
            try{
                requisicao = new ActiveXObject("Msxml2.XMLHTTP");
            }catch(e){
                try{
                    requisicao = new ActiveXObject("Microsoft.XMLHTTP");
                }catch(e){
                    alert("Navegador incompatível com AJAX");
                    return false;
                }
            }
        }

        //adiciona com relação ao pedido criado
        requisicao.onreadystatechange = function(){
            if(requisicao.readyState == 4){
                if(requisicao.responseText == '0'){
                    alert("Houve um erro ao atualizar a encomenda.");
                }else if(requisicao.responseText == -2){
                        document.getElementById("erros_input_2").innerHTML = "Encomendas devem possuir ao menos um produto!";
                }else{
                    alert("Encomenda de nº " + cod_ped_alt + " foi atualizada com sucesso!");
                    cod_ped_alt = -1;
                    location.href="encomendas.php";
                }
            }
        }

        var get_atrib = "?pedido=" + cod_ped_alt + "&data=" + data_enc + "&pago=" + pago;

        requisicao.open("GET", "requisicoes/enc_alter.php" + get_atrib, true);
        requisicao.send(); 
    }
    
    function muda_buscaOp(){
        var opcao = document.getElementsByName("buscaOp")[0].value;
        
        if(opcao.localeCompare("Código") == 0){
            $("input[name='search']").replaceWith("<input type=\"text\" placeholder=\"buscar por código..\" maxlength=\"10\" name=\"search\" oninput=\"replace_not_number('3')\" pattern=\"\\d{1,10}\" title=\"Apenas números. 1 a 10 dígitos.\" required>");
        }else if(opcao.localeCompare("Data de Entrega") == 0){
            $("input[name='search']").replaceWith("<input type=\"text\" placeholder=\"por data (dd/mm/aaaa)..\" maxlength=\"10\" name=\"search\" oninput=\"date_change()\" pattern=\"\\d{2}/\\d{2}/\\d{4}\" title=\"Apenas números. Formato final: dd/mm/aaaa\" required>");
        }else if(opcao.localeCompare("CPF Cliente") == 0){
            $("input[name='search']").replaceWith("<input type=\"text\" placeholder=\"buscar por cpf..\" maxlength=\"14\" name=\"search\" oninput=\"cpf_change()\" pattern=\"\\d{3}.\\d{3}.\\d{3}-\\d{2}\" title=\"Apenas números. 11 no total.\" required>");
        }
    }
    
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
</script>