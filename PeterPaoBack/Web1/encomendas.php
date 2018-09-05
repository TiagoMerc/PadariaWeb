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
            $query = "SELECT pedido.cod_pedido, nome_cliente, cliente.cpf_cliente, data_entrega, preco_total, data_pedido, pago FROM encomenda, pedido, cliente WHERE encomenda.cod_pedido = '$consulta' AND encomenda.cod_pedido = pedido.cod_pedido AND cliente.cpf_cliente = encomenda.cpf_cliente;";
        } else if (strcmp($opcao, "CPF Cliente") == 0) {
            $query = "SELECT pedido.cod_pedido, nome_cliente, cliente.cpf_cliente, data_entrega, preco_total, data_pedido, pago FROM encomenda, pedido, cliente WHERE cliente.cpf_cliente= '$consulta' AND encomenda.cod_pedido = pedido.cod_pedido AND cliente.cpf_cliente = encomenda.cpf_cliente;";
        } else {
            $query = "SELECT pedido.cod_pedido, nome_cliente, cliente.cpf_cliente, data_entrega, preco_total, data_pedido, pago FROM encomenda, pedido, cliente WHERE data_entrega = '$consulta' AND encomenda.cod_pedido = pedido.cod_pedido AND cliente.cpf_cliente = encomenda.cpf_cliente;";
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
                <a href="#" onclick="window.location.replace('home.php')"> <i class="fa fa-home" style="font-size:24px; padding-right:10px" ></i> Home </a>
                <a href="#" onclick="window.location.replace('clientes.php')"> <i class="fa fa-users" style="font-size:24px; padding-right:10px"></i>   Clientes </a>
                <a href="#" class="active"  onclick="window.location.replace('encomendas.php')"><i class="fa fa-book"style="font-size:24px; padding-right:10px"></i>  Encomendas</a>
                <a href="#" onclick="window.location.replace('vendas.php')"><i class="fa fa-shopping-basket"style="font-size:24px; padding-right:10px"></i>  Vendas </a> 
                <a href="#" onclick="window.location.replace('estoque.php')"><i class="fa fa-archive"style="font-size:24px; padding-right:10px"></i>   Estoque</a> 
                </div>
                <?php if($_SESSION['gerente'] == 1){?>
                <div class="vertical-menuGerente">
                <a href="#" onclick="window.location.replace('funcionarios.php')"> <i class="fa fa-user" style="font-size:24px; padding-right:10px"></i>   Funcionários </a>
                <a href="#"  onclick="window.location.replace('relatorio.php')"><i class="fa fa-clipboard"style="font-size:24px; padding-right:10px"></i>  Gerar Relatório</a>
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
                   
                }else if(x === "adicionar"){
                    document.getElementById("adicionar").style.display = 'block';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("alterar").style.display = 'none';
                    document.getElementById("consultarProd").style.display = 'none';
                    document.getElementById("consul").className = "botao";
                   document.getElementById("add").className = "botao active";
                }else if(x === "alterar"){
                    document.getElementById("alterar").style.display = 'block';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("consultarProd").style.display = 'none';
                    document.getElementById("adicionar").style.display = 'none';
                }else if(x === "addProd"){
                    document.getElementById("alterar").style.display = 'none';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("consultarProd").style.display = 'block';
                    document.getElementById("adicionar").style.display = 'none';
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
                                          <input type="text" placeholder="buscar por.." maxlength="50" name="search">
                                          <select id="buscaOp" name="buscaOp">
                                          <option>Código</option>
                                          <option >Data de Entrega</option>
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
                                            <th></th>  
                                          </tr>
                                            </thead>
                                        <tbody>
                                            
                                             <?php
                                                 while($obj = pg_fetch_object($result))
                                                {        ?>
                                                <tr class="linhaFora">
                                                <td><?php echo $obj->cod_pedido;?></td>
                                                <td><?php echo $obj->nome_cliente;?></td>    
                                                <td><?php
                                                 $obj->cpf_cliente = mask($obj->cpf_cliente, '###.###.###-##');
                                                 echo $obj->cpf_cliente; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($obj->data_entrega)); ?></td>
                                                <td><?php echo "R$ " . money_format('%.2n', $obj->preco_total); ?></td>     
                                                
                                                <td> 
                                                    <button class= "prod" data-toggle="collapse" data-target="#<?php echo $obj->cod_pedido; ?>"> <i class="fa fa-chevron-circle-down"style="color:green; font-size: 15pt"></i> </button>
                                                </td>   
                                                <td> 
                                                    <button class= "edit" type="edit" onclick="change('alterar')"> <i class="material-icons" style="color:white; font-size: 10pt">edit</i> </button>
                                                </td>  
                                                </tr>
                                            
                                            <tr id="<?php echo $obj->cod_pedido; ?>" class="collapse">
                                              <td colspan="7"> 
                                                <table class="produtosEncomeda" colspan="5">
                                                   
                                                    <tr> 
                                                        <td style="text-align: center"><b>Data do Pedido: </b><?php echo date('d/m/Y', strtotime($obj->data_pedido)); ?></td>
                                                        <td style="text-align: center" colspan="2">Pago:<b id="pago" onclick="changeText('<?php echo $obj->cod_pedido; ?>')">
                                                        <?php
                                                            if($obj->pago == '1') {
                                                                echo "Sim";
                                                            } else {
                                                                echo "Não";
                                                            }
                                                        ?></b></td>
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
                                        <form>
                                             <div class="containerBox">
                                                 <table id="formulario">
                                                    <tr>
                                                     <td rowspan="2" colspan="2"> 
                                                         <label for="produtos" class="titulo"><b>    Produtos </b></label>
                                                         <table class="prodConsul">
                                                         <tr>
                                                            <th>Código</th>
                                                            <th>Nome</th>
                                                            <th>Quantidade</th>
                                                            <th>Preço Unitário</th> 
                                                             <th>Editar</th>
                                                             <th>Excluir</th>
                                                         </tr>
                                                         <tr>
                                                            <td>24</td>
                                                            <td>Bolo de Cenoura</td>
                                                             <td>3</td>
                                                             <td>R$19,00</td>
                                                             <td> <button class= "edit" type="edit" style="background: none; border: none; box-shadow: none;" onclick="change('alterar')"> <i class="material-icons" style="color:green; font-size: 15pt;">edit</i> </button></td>
                                                              <td> <button class= "cancelar" type="cancel"> <i class="fa fa-close" style="font-size:20pt"></i> </button></td>
                                                         </tr>
                                                        <tr>
                                                            <td>34</td>
                                                            <td>Coxinha</td>
                                                             <td>30</td>
                                                             <td>R$4,00</td>
                                                            <td> <button class= "edit" type="edit" style="background: none; border: none; box-shadow: none;" onclick="change('alterar')"> <i class="material-icons" style="color:green; font-size: 15pt;">edit</i> </button></td>
                                                            <td> <button class= "cancelar" type="cancel"> <i class="fa fa-close" style="font-size:20pt"></i> </button></td>
                                                         </tr>
                                                        <tr>
                                                            <td>35</td>
                                                            <td>Pão de Queijo</td>
                                                             <td>20</td>
                                                             <td>R$2,00</td>
                                                            <td> <button class= "edit" type="edit" style="background: none; border: none; box-shadow: none;" onclick="change('alterar')"> <i class="material-icons" style="color:green; font-size: 15pt;">edit</i> </button></td>
                                                            <td> <button class= "cancelar" type="cancel"> <i class="fa fa-close" style="font-size:20pt"></i> </button></td>
                                                         </tr>     
                                                         </table></td>
                                                        <td><button type="addProd" onclick="change('addProd')">Adicionar Produtos <i class="fa fa-cart-plus" style="font-size:20pt"></i></button></td>
                                                     </tr>
                                                     <tr> 
                                                        <td> <p>Valor Total: R$ 217,00</p></td>
                                                     </tr>
                                                     <tr>
                                                         <td > <label for="cpf" class="titulo"><b>    CPF </b></label>
                                                        <input type="text"  id="cpf" oninput="cpf_replace(this.form.cpf.value,'cpf')" placeholder="xxx.xxx.xxx-xx" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="CPF tem 11 dígitos (Digite apenas números)" maxlenght="15" required autofocus ></td>
                                                        <td> <label for="dataEntrega" class="titulo"><b>    Data de Entrega </b></label>
                                                        <input type="date" placeholder="dd/mm/aaaa" name="dataEntre" required >
                                                         </td>
                                                         <td> <label for="pago" class="titulo"><b>   Pago </b></label>
                                                        <select id="setCargo" name="setCargo">
                                                          <option value="text">Não</option>
                                                          <option value="text">Sim</option>
                                                        </select>
                                                         </td>
                                                     </tr>
                                                 </table>
                                              </div>
                                            <div class="botoes" style=" margin: 7% 0 0 0;">
                                                <button type="reset" class="cancelbtn">Limpar</button>
                                                <button type="submit">Confirmar</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div id="alterar">
                                        <h3>Alterar Dados</h3>
                                        <form>
                                             <div class="containerBox">
                                                 <table id="formulario">
                                                    <tr>
                                                     <td rowspan="2" colspan="2"> 
                                                         <label for="produtos" class="titulo"><b>    Produtos </b></label>
                                                         <table class="prodConsul">
                                                         <tr>
                                                            <th>Código</th>
                                                            <th>Nome</th>
                                                            <th>Quantidade</th>
                                                            <th>Preço Unitário</th> 
                                                             <th>Editar</th>
                                                             <th>Excluir</th>
                                                         </tr>
                                                         <tr>
                                                            <td>24</td>
                                                            <td>Bolo de Cenoura</td>
                                                             <td>3</td>
                                                             <td>R$19,00</td>
                                                             <td> <button class= "edit" type="edit" style="background: none; border: none; box-shadow: none;" onclick="change('alterar')"> <i class="material-icons" style="color:green; font-size: 15pt;">edit</i> </button></td>
                                                              <td> <button class= "cancelar" type="cancel"> <i class="fa fa-close" style="font-size:20pt"></i> </button></td>
                                                         </tr>
                                                        <tr>
                                                            <td>34</td>
                                                            <td>Coxinha</td>
                                                             <td>30</td>
                                                             <td>R$4,00</td>
                                                            <td> <button class= "edit" type="edit" style="background: none; border: none; box-shadow: none;" onclick="change('alterar')"> <i class="material-icons" style="color:green; font-size: 15pt;">edit</i> </button></td>
                                                            <td> <button class= "cancelar" type="cancel"> <i class="fa fa-close" style="font-size:20pt"></i> </button></td>
                                                         </tr>
                                                        <tr>
                                                            <td>35</td>
                                                            <td>Pão de Queijo</td>
                                                             <td>20</td>
                                                             <td>R$2,00</td>
                                                            <td> <button class= "edit" type="edit" style="background: none; border: none; box-shadow: none;" onclick="change('alterar')"> <i class="material-icons" style="color:green; font-size: 15pt;">edit</i> </button></td>
                                                            <td> <button class= "cancelar" type="cancel"> <i class="fa fa-close" style="font-size:20pt"></i> </button></td>
                                                         </tr>     
                                                         </table></td>
                                                        <td><button type="addProd" onclick="change('addProd')">Adicionar Produtos <i class="fa fa-cart-plus" style="font-size:20pt"></i></button></td>
                                                     </tr>
                                                     <tr> 
                                                        <td> <p>Valor Total: R$ 217,00</p></td>
                                                     </tr>
                                                     <tr>
                                                        <td> <label for="dataEntrega" class="titulo"><b>    Data de Entrega </b></label>
                                                        <input type="date" placeholder="dd/mm/aaaa" name="dataEntre" required autofocus>
                                                         </td>
                                                         <td> <label for="pago" class="titulo"><b>   Pago </b></label>
                                                        <select id="setCargo" name="setCargo">
                                                          <option value="text">Não</option>
                                                          <option value="text">Sim</option>
                                                        </select>
                                                         </td>
                                                     </tr>
                                                 </table>
                                              </div>
                                            <div class="botoes" style=" margin: 7% 0 0 0;">
                                                <button type="voltar" class="voltar" onclick="change('consultar')"><i class="material-icons" style="color:white; font-size: 30pt">keyboard_return</i></button>
                                                <button type="submit">Confirmar</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div id="consultarProd">
                                        <h3>Adicionar Produtos na Encomenda</h3>
                                        <div class="busca">
                                          <input type="text" placeholder="buscar por.." maxlength="50" name="search_add" onchange="consulta_add()">
                                          <select id="buscaOp" name="buscaOp_add">
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
<script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
  <script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

<script language="javascript" type="text/javascript">
    //Função AJAX para add no estoque -->
    function consulta_add(){
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
            if(requisicao.readyState == 2){
                var tabela = document.getElementById('tabela_result_add');
                tabela.innerHTML = "<p>&#8195;Realizando a busca...</p>";
            }else if(requisicao.readyState == 4){
                var tabela = document.getElementById('tabela_result_add');
                tabela.innerHTML = requisicao.responseText;
            }
        }

        var busca = document.getElementsByName('search_add')[0].value;
        var modo = document.getElementsByName('buscaOp_add')[0].value;

        var get_atrib = "?busca=" + busca + "&modo=" + modo;

        requisicao.open("GET", "requisicoes/enc_add_list.php" + get_atrib, true);
        requisicao.send();
      $(document).ready(function(){
    $('#tabelaConsultar').DataTable({searching: false,
          "language": {
                "lengthMenu": "Mostrar _MENU_",
                "zeroRecords": "Nada encontrado",
                "info": "Página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro disponível",
                "infoFiltered": "(filtrado de _MAX_ registros no total)",
              "paginate":{
                  "previous": "Anterior",
                "next": "Próximo"
              }
            }
        });
  });
    }
    
    //Função de adição e criação de pedido
    function add_prod_pedido(cod_prod){
        /*var cod_ped = <?php if(isset($_SESSION['cod_pedido'])){echo -1;}else{echo -1;} ?>;
        alert(<?php echo $_SESSION['cod_pedido']; ?> + " yay");
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
            
            requisicao.onreadystatechange = function(){
                if(requisicao.readyState == 4){
                    <?php //$_SESSION['cod_pedido'] = requisicao.responseText; ?>
                    cod_ped = requisicao.responseText;
                }
            }
            
            var get_atrib = "?cpf=" + <?php //echo $_SESSION['cpf']; ?>;

            requisicao.open("GET", "requisicoes/create_ped.php" + get_atrib, true);
            requisicao.send();
        }else{
            alert("socorro");
        }*/
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
            if(requisicao.readyState == 4){
                alert("fez");
            }
        }
        
        var quantidade = document.getElementById("botaoAddQtdoEnco" + cod_prod).value;
        var get_atrib = "?cod=" + cod_prod + "&quant=" + quantidade;

        requisicao.open("GET", "requisicoes/enc_add_pedido.php" + get_atrib, true);
        requisicao.send();
        
    }
</script>