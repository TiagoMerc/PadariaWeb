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

    $data = date("d/m/Y");
    $query = "SELECT pedido.cod_pedido, nome_cliente, cliente.cpf_cliente, data_entrega, preco_total FROM encomenda, pedido, cliente WHERE data_entrega = '$data' AND encomenda.cod_pedido = pedido.cod_pedido AND cliente.cpf_cliente = encomenda.cpf_cliente ORDER BY pedido.cod_pedido;";
    
    $result = pg_query($conexao, $query);
    
      
    pg_close($conexao);
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Peter Pão: Página Inicial</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
       <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/table.css">
        <link rel="stylesheet" href="css/home.css">
         
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
                <a href="#" class="active"> <i class="fa fa-home" style="font-size:24px; padding-right:10px" ></i> Home </a>
                <a href="#" onclick="window.location.replace('clientes.php')"> <i class="fa fa-users" style="font-size:24px; padding-right:10px"></i>   Clientes </a>
                <a href="#" onclick="window.location.replace('encomendas.php')"><i class="fa fa-book"style="font-size:24px; padding-right:10px"></i>  Encomendas</a>
                <a href="#" onclick="window.location.replace('vendas.php')"><i class="fa fa-shopping-basket"style="font-size:24px; padding-right:10px"></i>  Vendas</a> 
                <a href="#" onclick="window.location.replace('estoque.php')"><i class="fa fa-archive"style="font-size:24px; padding-right:10px"></i>   Estoque</a> 
                </div>
                
                <div class="vertical-menuGerente">
                <?php if($_SESSION['gerente'] == 1){?>
                <a href="#" onclick="window.location.replace('funcionarios.php')"> <i class="fa fa-user" style="font-size:24px; padding-right:10px"></i>   Funcionários </a>
                <a href="#"  onclick="window.location.replace('relatorio.php')"><i class="fa fa-clipboard"style="font-size:24px; padding-right:10px"></i>  Gerar Relatório</a>
                    
                <?php } ?>
                </div>
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
                <div class="container">
                <h2>Bem Vindo(a) à Página Inicial, <?php echo $_SESSION['nome'] ?></h2>
                    <h3> Hoje é <script language=javascript type="text/javascript">
                   now = new Date
                    mlist = [ "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro" ];
                    slist = [ "Domingo", "Segunda-Feira", "Terça-Feira", "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "Sábado"];    
                   document.write (slist[now.getDay()] + ", " +  now.getDate() + " de " + (mlist[now.getMonth()]) + " de " + now.getFullYear());
               </script></h3>
                    <h3 class="dia"> Acesso iniciado às <?php echo $_SESSION['hora_login']?></h3>
                    <h4>Próximas Encomendas para Hoje:</h4>
                    <div class="encomendasC">
                        <table id="tabelaConteudo">
                            <thead>
                              <tr>
                                <th>Código</th>  
                                <th>Cliente</th>
                                <th>CPF</th>
                                <th>Data da Entrega</th>
                                <th>Valor Total</th>    
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                             while($obj = pg_fetch_object($result))
                            {        ?>
                            <tr>
                            <td><?php echo $obj->cod_pedido;?></td>
                            <td><?php echo $obj->nome_cliente;?></td>    
                            <td><?php echo $obj->cpf_cliente; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($obj->data_entrega)); ?></td>
                            <td><?php echo "R$ " . money_format('%.2n', $obj->preco_total); ?></td>     

                            <?php         
                            }
                            ?>
                            
                            </tbody>
                        </table>
                    </div>
                </div>   
            </div>
          <script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
  <script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
  <script>
  $(document).ready(function(){
      $('#tabelaConteudo').DataTable({searching: false,
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