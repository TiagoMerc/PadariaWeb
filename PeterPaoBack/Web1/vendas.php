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

    if (isset($_POST["search"])) {
        $consulta = $_POST["search"];
        $opcao = $_POST["buscaOp"]; 
        if (strcmp($opcao, "Código") == 0) {
            $query = "SELECT cod_pedido, cpf_funcio, data_venda, preco_total FROM pedido WHERE cod_pedido = $consulta ORDER BY preco_total;";
        } else if (strcmp($opcao, "CPF Funcionário") == 0) {
            $query = "SELECT cod_pedido, cpf_funcio, data_venda, preco_total FROM pedido WHERE cpf_funcio = '$consulta' ORDER BY preco_total;";
        } else {
            $query = "SELECT cod_pedido, cpf_funcio, data_venda, preco_total FROM pedido WHERE data_venda = '$consulta' ORDER BY preco_total;";
        }   
        
        $vendas = pg_query($conexao, $query);
    
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
        <script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
  <script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
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
                <a href="#"  class="active"  onclick="window.location.replace('vendas.php')"><i class="fa fa-shopping-basket"style="font-size:24px; padding-right:10px"></i>  Vendas </a> 
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
                                        <form name = "consultar" method="post">  
                                          <input type="text" placeholder="buscar por.." maxlength="50" name="search">
                                        <select id="buscaOp" name="buscaOp">
                                          <option value="text">Data</option>
                                          <option value="text">CPF Funcionário</option>
                                          <option value="text">Código</option>
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
                                            <td><?php echo "R$ " . money_format('%.2n', $ped->preco_total); ?></td>    
                                            <td> <button class= "prod" data-toggle="collapse" data-target="#<?php echo $ped->cod_pedido; ?>"> <i class="fa fa-chevron-circle-down"style="color:green; font-size: 15pt"></i> </button>
                                            </td> 
                                          </tr>
                                            <tr id="<?php echo $ped->cod_pedido; ?>" class="collapse">
                                                <td colspan="5"> 
                                                    <table class="produtos" >
                                                        <thead>
                                                        <tr>
                                                            <td><b>Código</b></td>
                                                            <td style="text-align: left"><b>Nome</b></td>
                                                            <td><b>Quantidade</b></td>
                                                            
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

  <script>
  $(document).ready(function(){
    $('#tabelaConsultarVendas').DataTable({searching: false,
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