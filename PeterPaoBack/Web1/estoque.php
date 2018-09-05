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
     include('conectar.php');
     /* Adaptado de http://blog.clares.com.br/php-mascara-cnpj-cpf-data-e-qualquer-outra-coisa/ */
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

    if (isset($_POST["search"])) {
        $consulta = $_POST["search"];
        $opcao = $_POST["buscaOp"]; 

        if (strcmp($opcao, "Código") == 0) {
            $query = "SELECT cod_prod, nome_prod, quantidade_estoque, categoria, preco_unitario FROM produto WHERE cod_prod = '$consulta' ORDER BY cod_prod;";
        } else if (strcmp($opcao, "Nome") == 0) {
            $query = "SELECT cod_prod, nome_prod, quantidade_estoque, categoria, preco_unitario FROM produto WHERE nome_prod ILIKE '$consulta%'  ORDER BY cod_prod;";
        } else {
            $query = "SELECT cod_prod, nome_prod, quantidade_estoque, categoria, preco_unitario FROM produto WHERE categoria = '$consulta' ORDER BY cod_prod;";
        }   
        $result = pg_query($conexao, $query);
    }

    if(isset($_POST['cadastro'])) {
       if($_POST['cadastro'] == "estoque") {      
            $nome = $_POST['nome'];
            $categoria = $_POST['setCargo'];
            $quantidade = $_POST['quanti'];
            $preco_unitario = $_POST['preco_ins'];
            $descricao = $_POST['descricao_ins'];
            $insert = "INSERT INTO produto(
            cod_prod, nome_prod, quantidade_estoque, categoria, preco_unitario, descricao)
            VALUES (DEFAULT, '$nome', '$quantidade','$categoria', '$preco_unitario','$descricao');";
            $res = pg_exec($conexao, $insert); 
            pg_query($conexao, $sql); 
            //"$qtd_linhas" recebe a quantidade de Linhas Afetadas pela Inserção 
            $qtd_linhas = pg_affected_rows($res); 
                                               
             if ($qtd_linhas == 0) { 
                echo '<script language="javascript"> alert("Não foi possível inserir o Produto. Verique se o produto já está cadastrado no sistema!") </script>';
            } else {
                echo '<script language="javascript"> alert("Produto inserido com sucesso!") </script>';
                echo '<script language="javascript"> location.href="estoque.php" </script>';
            }
       }   
    }

    if($_GET["tipoBusca2"] == "produz"){
        if (isset($_POST["search2"])) {
            $consulta = $_POST["search2"];
            $opcao = $_POST["buscaOp2"]; 
            if (strcmp($opcao, "Data") == 0) {
                $query = "SELECT cpf_funcio, produto.cod_prod, nome_prod, data_producao, quantidade_prod FROM produto, produz WHERE data_producao = '$consulta' AND produto.cod_prod = produz.cod_prod;";
            } else if (strcmp($opcao, "Código") == 0) {
                $query = "SELECT cpf_funcio, produto.cod_prod, nome_prod, data_producao, quantidade_prod FROM produto, produz WHERE produto.cod_prod = '$consulta' AND produto.cod_prod = produz.cod_prod;";
            } else if(strcmp($opcao, "Nome") == 0){
                $query = "SELECT cpf_funcio, produto.cod_prod, nome_prod, data_producao, quantidade_prod FROM produto, produz WHERE nome_prod ILIKE '$consulta%' AND produto.cod_prod = produz.cod_prod;";
            }else{
                $query = "SELECT cpf_funcio, produto.cod_prod, nome_prod, data_producao, quantidade_prod FROM produto, produz WHERE cpf_funcio = '$consulta' AND produto.cod_prod = produz.cod_prod;";
            }   
            
            
            $result2 = pg_query($conexao, $query);
            
        }
    }

    if(isset($_POST["submit_add"])){
        $quanti_add = $_POST["quanti_add"];
        $cod_add = $_POST["cod_prod_add"];
        
        $query = "UPDATE produto SET quantidade_estoque = '$quanti_add' + (SELECT quantidade_estoque FROM produto WHERE cod_prod = '$cod_add') WHERE cod_prod = '$cod_add';";
        
        $resultado_add = pg_exec($conexao, $query);
        $n_linhas = pg_affected_rows($resultado_add);
         
        if($n_linhas == 0){
            echo '<script language="javascript"> alert("Não foi possível alterar o estoque") </script>';
            echo '<script language="javascript"> location.href="estoque.php" </script>';
        }else{
            if(isset($_POST["produzido_add"])){
                
                $cpf_produz = $_SESSION['cpf'];
                $data = date('d/m/Y');
                
                $insert = "INSERT INTO public.produz(cod_prod, cpf_funcio, data_producao, quantidade_prod) 
                VALUES ($cod_add, '$cpf_produz', '$data', $quanti_add);";
                   
                $resultado_prod = pg_exec($conexao, $insert);
                $n_linhas_prod = pg_affected_rows($resultado_prod);
                
                if($n_linhas_prod == 0){
                    echo '<script language="javascript"> alert("Adicão no estoque realizada. Não foi possível adicionar essa nova produção!") </script>';
                    echo '<script language="javascript"> location.href="estoque.php" </script>';
                }else{
                    echo '<script language="javascript"> alert("Adições no estoque e produção realizadas com sucesso!") </script>';
                    echo '<script language="javascript"> location.href="estoque.php" </script>';   
                     
                }
            }else{
                echo '<script language="javascript"> alert("Adição no estoque realizada com sucesso!") </script>';
                echo '<script language="javascript"> location.href="estoque.php" </script>';   
            }
        }
    }

    if(strcmp($_GET["estoque"], "alterar") == 0){
        $cod_prod = $_GET['cod_alt'];
        
        $alt = "SELECT nome_prod, categoria, quantidade_estoque, preco_unitario, descricao FROM produto WHERE cod_prod = '$cod_prod'";
        
        $resultado_alt = pg_query($conexao, $alt);
        $prod = pg_fetch_object($resultado_alt);
        
        $nome_prod = $prod->nome_prod;
        $categoria = $prod->categoria;
        $quant = $prod->quantidade_estoque;
        $preco = $prod->preco_unitario;
        $desc = $prod->descricao;
    }

    if(isset($_POST['submit_alt'])){
        $cod_prod_up = $_POST['cod_alte'];
        $nome_prod = $_POST['nome_alt'];
        $categoria = $_POST['setCat_alt'];
        $quant = $_POST['quanti_alt'];
        $preco = $_POST['preco_alt'];
        $desc = $_POST['descricao_alt'];
        
        $up = " UPDATE public.produto
                SET nome_prod='$nome_prod', categoria='$categoria', descricao='$desc', preco_unitario=$preco, quantidade_estoque=$quant
                WHERE cod_prod = $cod_prod_up;";
        
        $res = pg_exec($conexao, $up);
        
        $nlinhas = pg_affected_rows($res);
        
        if($nlinhas == 0){
            echo '<script language="javascript"> alert("Não foi possível alterar o produto. Verique se o codigo inserido é válido!") </script>';
            echo '<script language="javascript"> location.href="estoque.php" </script>';
        } else {
            echo '<script language="javascript"> alert("Produto alterado com sucesso!") </script>';
            echo '<script language="javascript"> location.href="estoque.php" </script>';
        }
        
    }

    pg_close($conexao);
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Peter Pão: Estoque</title>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
       <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/table.css">
        <link rel="stylesheet" href="css/clientes.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <script>        
            window.onload = function(){
                if (window.location.href.search("tipoBusca2=produz") > -1) {
                    change("producao");
                }else if(window.location.href.search("estoque=alterar") > -1) {
                    change("alterar");  
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
                <a href="#" onclick="window.location.replace('home.php')"> <i class="fa fa-home" style="font-size:24px; padding-right:10px" ></i> Home </a>
                <a href="#" onclick="window.location.replace('clientes.php')"> <i class="fa fa-users" style="font-size:24px; padding-right:10px"></i>   Clientes </a>
                <a href="#" onclick="window.location.replace('encomendas.php')"><i class="fa fa-book"style="font-size:24px; padding-right:10px"></i>  Encomendas</a>
                <a href="#" onclick="window.location.replace('vendas.php')"><i class="fa fa-shopping-basket"style="font-size:24px; padding-right:10px"></i>  Vendas</a> 
                <a href="#" class="active" onclick="window.location.replace('estoque.php')"><i class="fa fa-archive"style="font-size:24px; padding-right:10px"></i>   Estoque</a> 
                </div>
                <?php if($_SESSION['gerente'] == 1){?>
                <div class="vertical-menuGerente">
                <a href="#" onclick="window.location.replace('funcionarios.php')"> <i class="fa fa-user" style="font-size:24px; padding-right:10px"></i>   Funcionários </a>
                <a href="#"  onclick="window.location.replace('relatorio.php')"><i class="fa fa-clipboard"style="font-size:24px; padding-right:10px"></i>  Gerar Relatório</a>
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
        <script>
            function change(x){
               if(x === "consultar"){
                   document.getElementById("consultar").style.display = 'block';
                   document.getElementById("adicionar").style.display = 'none';
                   document.getElementById("producao").style.display = 'none';
                   document.getElementById("alterar").style.display = 'none';
                   document.getElementById("consul").className = "botao active";
                   document.getElementById("add").className = "botao";
                   document.getElementById("botaoProduzido").className = "botao";
                   
                }else if(x === "adicionar"){
                    document.getElementById("adicionar").style.display = 'block';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("producao").style.display = 'none';
                    document.getElementById("alterar").style.display = 'none';
                    document.getElementById("consul").className = "botao";
                    document.getElementById("botaoProduzido").className = "botao";
                   document.getElementById("add").className = "botao active";
                }else if(x === "alterar"){
                    document.getElementById("alterar").style.display = 'block';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("adicionar").style.display = 'none';
                    document.getElementById("producao").style.display = 'none';
                }else if(x === "producao"){
                    document.getElementById("adicionar").style.display = 'none';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("producao").style.display = 'block';
                    document.getElementById("alterar").style.display = 'none';
                    document.getElementById("consul").className = "botao";
                    document.getElementById("add").className = "botao";
                    document.getElementById("botaoProduzido").className = "botao active";
                }
                
            }
            function alter_url(obj){
             
                    str = "?estoque=alterar&cod_alt=" + obj;
                    window.location.replace(window.location.href + str);

            }
            </script>
            <script>
                function modal(obj){
                    document.getElementById("addEstoque").style.display = "block";
                    document.getElementById("cod_prod_add").value = obj;
                }
                function Myclose(){
                     document.getElementById("addEstoque").style.display = "none";
                }
                 window.onclick = function(event) {
                    if (event.target == document.getElementById('addEstoque')) {
                        document.getElementById('addEstoque').style.display = "none";
                    }
                }
                </script>
        </div>
            <div class="col-md-9  gambi">
                <div class="container" style="background-image: url('Img/croissant.png');">
                <h2>Estoque</h2>
                    <div class="conteudo">
                       <table id="menuTabela">
                            <tr>
                                <td id="consul" class="botao active"> <a href="estoque.php" onclick="change('consultar')"> Consultar</a></td>
                                <td rowspan="7" style = "width: 100%"> 
                                     <div id="consultar">
                                        <form class="busca" method="post">
                                          <input type="text" placeholder="buscar por.." maxlength="50" name="search">
                                          <select id="buscaOp" name="buscaOp">
                                          <option>Código</option>
                                          <option>Nome</option>
                                          <option>Categoria</option>
                                        </select>
                                        </form>
                                        <table id="tabelaConsultar">
                                        <thead>
                                          <tr>
                                            <th>Código</th>
                                            <th>Nome</th>
                                            <th>Quantidade</th>
                                            <th>Categoria</th>  
                                            <th>Preço Unitário</th> 
                                            <th>Add</th>  
                                            <th></th> 
                                          </tr>
                                            
                                        </thead>
                                         <tbody>
                                            
                                             <?php
                                                 while($obj = pg_fetch_object($result))
                                                {        ?>
                                                <tr>
                                                <td><?php echo $obj->cod_prod;?></td>
                                                <td style="text-align:left; text-indent: 17px;"><?php echo $obj->nome_prod;?></td>    
                                                <td><?php echo $obj->quantidade_estoque; ?></td>
                                                <td><?php echo $obj->categoria; ?></td>
                                                <td style="text-align:left; text-indent: 17px;"><?php $obj->preco_unitario = "R$ " . money_format('%.2n', $obj->preco_unitario);echo $obj->preco_unitario;?></td>  
                                                <td> <button class= "plus" type="plus" id="plus" onclick="modal(<?php echo $obj->cod_prod; ?>)"><i class="fa fa-plus" style="font-size:20px"></i></button></td>  
                                                <td> <button class= "edit" type="edit" onclick="alter_url(<?php echo $obj->cod_prod; ?>)"> <i class="material-icons" style="color:white; font-size: 10pt">edit</i> </button></td>  
                                              </tr>
                                                <?php         
                                                }
                                                ?>   
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="adicionar">
                                        <form method="post">
                                             <div class="containerBox">
                                                 <table id="formulario">
                                                    <tr>
                                                     <td>
                                                        <label for="Nome" class="titulo"><b>    Nome </b></label>
                                                        <input type="text" placeholder="Nome" name="nome" maxlength="50" required autofocus >
                                                        </td>
                                                    <td>
                                                        <label for="categoria" class="titulo"> <b>   Categoria    </b></label>
                                                        <select id="setCargo" name="setCargo">
                                                          <option >Pães</option>
                                                          <option >Tortas</option>
                                                          <option >Bolos</option>
                                                          <option >Doces</option>
                                                          <option >Bebidas</option>
                                                          <option >Ingredientes</option>
                                                        </select>
                                                    </td>    
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td>
                                                         <label for="quant" class="titulo"><b>  Quantidade  </b></label>
                                                        <input type="number" placeholder="xxx" name="quanti" title="Apenas números" required>
                                                         </td>
                                                         <td>
                                                             <label for="preco" class="titulo"> <b>  Preço Unitário    </b></label>
                                                             <input type="number" placeholder="xxxx,xx" step="0.01" name='preco_ins' required>                       
                                                         </td>
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td colspan="2" >
                                                            <label for="desc" class="titulo"> <b>  Descrição  </b></label>
                                                            <input type="text" placeholder="descrição"  maxlength="300" name="descricao_ins">
                                                         </td>
                                                     </tr>
                                                 </table>
                                              </div>
                                            <div class="botoes" style="margin-top: 10%">
                                                <button type="reset" class="cancelbtn">Limpar</button>
                                                <button type="submit">Adicionar</button>
                                            </div>
                                            <input type="hidden" name="cadastro" value="estoque">
                                        </form>
                                    </div>
                                    <div id="alterar">
                                        <h3>Alterar Dados</h3>
                                        <form method="post">
                                             <div class="containerBox">
                                                 <input type="hidden" name="cod_alte" value="<?php echo $cod_prod ?>">
                                                 <table id="formulario">
                                                     <tr>
                                                     <td>
                                                        <label for="Nome" class="titulo"><b>    Nome </b></label>
                                                        <input type="text" placeholder="Nome" name="nome_alt" maxlength="50" value="<?php echo $nome_prod; ?>" required autofocus >
                                                        </td>
                                                    <td>
                                                        <label for="categoria" class="titulo"> <b>   Categoria    </b></label>
                                                        <select id="setCargo" name="setCat_alt">
                                                          <option <?php if(strcmp("Pães", $categoria) == 0){echo 'selected = "selected"';} ?> >Pães</option>
                                                          <option <?php if(strcmp("Tortas", $categoria) == 0){echo 'selected = "selected"';} ?> >Tortas</option>
                                                          <option <?php if(strcmp("Bolos", $categoria) == 0){echo 'selected = "selected"';} ?> >Bolos</option>
                                                          <option <?php if(strcmp("Doces", $categoria) == 0){echo 'selected = "selected"';} ?> >Doces</option>
                                                          <option <?php if(strcmp("Babidas", $categoria) == 0){echo 'selected = "selected"';} ?> >Babidas</option>
                                                          <option <?php if(strcmp("Ingredientes", $categoria) == 0){echo 'selected = "selected"';} ?> >Ingredientes</option>
                                                        </select>
                                                    </td>    
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td>
                                                         <label for="quant" class="titulo"><b>  Quantidade  </b></label>
                                                        <input type="number" placeholder="xxx" name="quanti_alt" value="<?php echo $quant; ?>" required>
                                                         </td>
                                                         <td>
                                                             <label for="preco" class="titulo"> <b>  Preço Unitário    </b></label>
                                                             <input type="number" placeholder="xxxx,xx" step="0.01" name="preco_alt" value="<?php echo $preco; ?>"  required>                       
                                                         </td>
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td colspan="2" >
                                                            <label for="desc" class="titulo"> <b>  Descrição  </b></label>
                                                            <input type="text" placeholder="descrição" name="descricao_alt" value="<?php echo $desc; ?>"  maxlength="300">
                                                         </td>
                                                     </tr>
                                                 </table>
                                              </div>
                                            <div class="botoes" style="margin-top: 6%">
                                                <button type="button" class="voltar" onclick="window.location.replace('estoque.php')"><i class="material-icons" style="color:white; font-size: 30pt">keyboard_return</i></button>
                                                <button type="submit" name="submit_alt">Confirmar</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div id="producao">
                                        <form class="busca" method="post">
                                        <input type="hidden" name="tipoBusca2" value="produz">
                                          <input type="text" placeholder="buscar por.." maxlength="50" name="search2">
                                          <select id="buscaOp" name="buscaOp2">
                                          <option >Data</option>
                                          <option >Código</option>
                                          <option >Nome</option>
                                          <option >CPF Funcio</option>      
                                        </select>
                                        </form>
                                        <table id="tabelaProducao">
                                            <thead>
                                              <tr>
                                                <th>CPF do Funcionário</th>  
                                                <th>Data</th>
                                                <th>Código</th>
                                                <th>Nome</th>
                                                <th>Quantidade</th>  
                                              </tr>
                                            </thead>
                                            <tbody>
                                              <?php
                                                     while($obj = pg_fetch_object($result2))
                                                    {        ?>
                                                    <tr>
                                                    <td><?php echo $obj->cpf_funcio;?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($obj->data_producao)); ?></td> 
                                                    <td><?php echo $obj->cod_prod; ?></td>
                                                    <td style="text-align:left; text-indent: 17px;"><?php echo $obj->nome_prod; ?></td>     
                                                    <td><?php echo $obj->quantidade_prod; ?></td>  
                                                  </tr>
                                                    <?php         
                                                    }

                                                    ?> 
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="addEstoque" class="addEstoque">
                                        <div class="coisoAdd" id="coiso">
                                            <button class="close" onclick="Myclose()"><i class="fa fa-close" style="font-size:24px;color:red"></i></button>
                                            <form name="add_estoque" method="post">
                                                <label for="quant" class="titulo"><b>  Quantidade a ser adicionada </b></label>
                                                <input type="number" placeholder="xxx" name="quanti_add" required>
                                                <label id="checkProd">
                                                <input type="checkbox" name="produzido_add">Produzido
                                                </label>
                                                <input type="hidden" name="cod_prod_add" id="cod_prod_add" value="?">
                                                <button type="submit" name="submit_add">Adicionar</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                        
                           <tr>
                                <td id="add" class="botao" style="text-align: center"> <a href="#" onclick="change('adicionar')"> Adicionar (Novo)</a></td>
                           </tr>
                           <tr>
                               <td id="botaoProduzido" class="botao" style="text-align: center"> <a href="estoque.php?tipoBusca2=produz" onclick="change('producao')"> Produção </a></td>
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
                  <script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
  <script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
  <script>
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
  </script>
 <script>
  $(document).ready(function(){
    $('#tabelaProducao').DataTable({searching: false,
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
  </script>        
    </body>
</html>