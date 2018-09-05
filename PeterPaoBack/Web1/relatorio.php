<?php
    session_start();
    if(!(isset($_SESSION['log']))){
        header("Location: login.php?erro_login=2");
    }

    
    if($_SESSION['gerente'] != 1){
        header("Location: home.php?erro_login=3");
    }
?>

<?php
    include('conectar.php');
    if(isset($_POST["tipoBusca"])){
        if($_POST["tipoBusca"] == "vendas"){
            if (isset($_POST["search"])) {
                $consulta = $_POST["search"];
                $dataInicio = $_POST["pordata"];
                $dataFim = $_POST["pordataate"];
                $opcao = $_POST["buscaOp"]; 
                if (strcmp($opcao, "Código") == 0) {
                    $query = "SELECT produto.cod_prod, nome_prod, quantidade_estoque, categoria, descricao, preco_unitario, COUNT(pedido.cod_pedido) AS quant_pedidos FROM possui, produto, pedido WHERE possui.cod_prod = produto.cod_prod AND produto.cod_prod = '$consulta' AND pedido.cod_pedido = possui.cod_pedido AND pedido.data_venda BETWEEN '$dataInicio' AND '$dataFim' GROUP BY produto.cod_prod;";
                } else if (strcmp($opcao, "Nome") == 0) {
                    $query = "SELECT produto.cod_prod, nome_prod, quantidade_estoque, categoria, descricao, preco_unitario, COUNT(pedido.cod_pedido) AS quant_pedidos FROM possui, produto, pedido WHERE possui.cod_prod = produto.cod_prod AND nome_prod ILIKE '$consulta%' AND pedido.cod_pedido = possui.cod_pedido AND pedido.data_venda BETWEEN '$dataInicio' AND '$dataFim' GROUP BY produto.cod_prod;";
                } else {
                    $query = "SELECT produto.cod_prod, nome_prod, quantidade_estoque, categoria, descricao, preco_unitario, COUNT(pedido.cod_pedido) AS quant_pedidos FROM possui, produto, pedido WHERE possui.cod_prod = produto.cod_prod AND categoria = '$consulta' AND pedido.cod_pedido = possui.cod_pedido AND pedido.data_venda BETWEEN '$dataInicio' AND '$dataFim' GROUP BY produto.cod_prod;";
                }   
                $result = pg_query($conexao, $query);
            
            }
        } 
                
    }
    
    if($_GET["tipoBusca2"] == "produz"){
                if (isset($_GET["search2"])) {
                    $consulta = $_GET["search2"];
                    $dataInicio = $_GET["pordata2"];
                    $dataFim = $_GET["pordataate2"];
                    $opcao = $_GET["buscaOp2"]; 
                    if (strcmp($opcao, "Código") == 0) {
                        $query = "SELECT produto.cod_prod, nome_prod, quantidade_estoque, categoria, descricao, preco_unitario, SUM(produz.quantidade_prod) AS quant_produz FROM produz, produto WHERE produz.cod_prod = produto.cod_prod AND produto.cod_prod = '$consulta' AND produz.data_producao BETWEEN '$dataInicio' AND '$dataFim' GROUP BY produto.cod_prod;";
                    } else if (strcmp($opcao, "Nome") == 0) {
                        $query = "SELECT produto.cod_prod, nome_prod, quantidade_estoque, categoria, descricao, preco_unitario, SUM(produz.quantidade_prod) AS quant_produz FROM produz, produto WHERE produz.cod_prod = produto.cod_prod AND nome_prod ILIKE '$consulta%' AND produz.data_producao BETWEEN '$dataInicio' AND '$dataFim' GROUP BY produto.cod_prod;";
                    } else {
                        $query = "SELECT produto.cod_prod, nome_prod, quantidade_estoque, categoria, descricao, preco_unitario, SUM(produz.quantidade_prod) AS quant_produz FROM produz, produto WHERE produz.cod_prod = produto.cod_prod AND categoria = '$consulta' AND produz.data_producao BETWEEN '$dataInicio' AND '$dataFim' GROUP BY produto.cod_prod;";
                    }   
                    $result2 = pg_query($conexao, $query);

                }

            }
    if (isset($_GET["pordata3"])) {
        $dataInicio = $_GET["pordata3"];
        $dataFim = $_GET["pordataate3"];
        $tipo = $_GET["tipoRelatorio"];
        require('FPDF/fpdf181/fpdf.php');
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Times');
        if($tipo == "vendas"){
            $pdf->SetFontSize(18);
            $dataFim = date('d-m-Y', strtotime($dataFim));
            $dataInicio = date('d-m-Y', strtotime($dataInicio));
            $data = date('d-m-Y', strtotime($dataInicio));
            $pdf->Cell(100,10, "Relatório de Vendas do dia $data ao dia $dataFim", 0, 1);
            $pdf->SetFontSize(11);
    
            while($data != $dataFim){
                $pdf->Cell(60,7,"Vendas do Dia: $data",0,1,'L');
                $query = "SELECT produto.cod_prod, nome_prod, preco_unitario, SUM(possui.quantidade_possui) AS quantidade FROM possui, produto, pedido WHERE possui.cod_prod = produto.cod_prod AND pedido.cod_pedido = possui.cod_pedido AND pedido.data_venda = '$data' GROUP BY produto.cod_prod ORDER BY produto.cod_prod;";
                $result2 = pg_query($conexao, $query);
                $numregs=pg_numrows($result2);
                if($numregs == 0){
                    $pdf->Cell(60,4,'Não houve Vendas nesse dia',0,0,'L');
                }
                $i=0;
                $pdf->Cell(15,5,"Código",0,0,'C');
                $pdf->Cell(55,5,"Nome",0,0,'L');
                $pdf->Cell(32,5,"Preço Unitário",0,0,'L');
                $pdf->Cell(45,5,"Qtd total Vendida",0,0,'L');
                $pdf->Cell(20,5,"Total",0,1,'L');
                 while($i<$numregs)
                {
                    $cod=pg_result($result2,$i,'cod_prod');
                    $nome=pg_result($result2,$i,'nome_prod');
                    $preco=pg_result($result2,$i,'preco_unitario');
                    $qtd_total=pg_result($result2,$i,'quantidade');
                    $vendas = $qtd_total * $preco;
                    $vendas = money_format('%.2n',$vendas); 
                    $preco = money_format('%.2n',$preco);
                    $pdf->Cell(15,4,$cod,0,0,'C');
                    $pdf->Cell(60,4,$nome,0,0,'L');
                    $pdf->Cell(35,4,"R$ $preco",0,0,'L');
                    $pdf->Cell(35,4,$qtd_total,0,0,'L');
                    $pdf->Cell(20,4,"R$ $vendas",0,1,'L');
                    $i++;
                }   
                $pdf->Cell(0,3,'----------------------------------------------------------------------------------------------------------------------------------------------------',0,1);
                $data = date('d-m-Y', strtotime("+1 days",strtotime($data))); 
            }
            $pdf->SetFontSize(18);
            $pdf->Cell(60,15,"Tabela de Vendas realizadas entre $dataInicio e $dataFim ",0,1,'L');
            $pdf->SetFontSize(11);
            $query = "SELECT produto.cod_prod, nome_prod, categoria, preco_unitario, COUNT(pedido.cod_pedido) AS quant_pedidos, SUM(possui.quantidade_possui) AS quantidade FROM possui, produto, pedido WHERE possui.cod_prod = produto.cod_prod AND pedido.cod_pedido = possui.cod_pedido AND pedido.data_venda BETWEEN '$dataInicio' AND '$dataFim' GROUP BY produto.cod_prod ORDER BY produto.cod_prod;";
            $result2 = pg_query($conexao, $query);
            $numregs=pg_numrows($result2);
            $i=0;
            $pdf->Cell(15,7,"Código",1,0,'C');
                $pdf->Cell(60,7,"Nome",1,0,'L');
                $pdf->Cell(25,7,"Preço Unitário",1,0,'L');
                $pdf->Cell(25,7,"Qtd Pedidos",1,0,'L');
                $pdf->Cell(35,7,"Qtd total Vendida",1,0,'L');
                $pdf->Cell(30,7,"Total",1,1,'L');
            while($i<$numregs)
            {
                $cod=pg_result($result2,$i,'cod_prod');
                $nome=pg_result($result2,$i,'nome_prod');
                $categoria=pg_result($result2,$i,'categoria');
                $preco=pg_result($result2,$i,'preco_unitario');
                $qtd_pedido=pg_result($result2,$i,'quant_pedidos');
                $qtd_total=pg_result($result2,$i,'quantidade');
                $vendas = $qtd_total * $preco;
                $vendas = money_format('%.2n',$vendas); 
                $preco = money_format('%.2n',$preco);
                $pdf->Cell(15,7,$cod,1,0,'C');
                $pdf->Cell(60,7,$nome,1,0,'L');
                $pdf->Cell(25,7,"R$ $preco" ,1,0,'L');
                $pdf->Cell(25,7,$qtd_pedido,1,0,'L');
                $pdf->Cell(35,7,$qtd_total,1,0,'L');
                $pdf->Cell(30,7,"R$ $vendas",1,1,'L');
                $i++;
            }    
            $pdf->Output("I", "Vendas-$dataInicio-$dataFim.pdf");
            
        }else if($tipo == "producao"){
            $pdf->SetFontSize(18);
            $dataFim = date('d-m-Y', strtotime($dataFim));
            $dataInicio = date('d-m-Y', strtotime($dataInicio));
            $data = date('d-m-Y', strtotime($dataInicio));
            $pdf->Cell(100,10, "Relatório de Produção do dia $data ao dia $dataFim", 0, 1);
            $pdf->SetFontSize(11);
    
            while($data != $dataFim){
                $pdf->Cell(60,7,"Produção do Dia: $data",0,1,'L');
                $query = "SELECT funcionario.cpf_funcio, nome_funcio, produto.cod_prod, nome_prod, SUM(produz.quantidade_prod) AS quant_produz FROM produz, produto, funcionario WHERE produz.cod_prod = produto.cod_prod AND funcionario.cpf_funcio = produz.cpf_funcio AND produz.data_producao = '$data' GROUP BY produto.cod_prod, funcionario.cpf_funcio ORDER BY produto.cod_prod;";
                $result2 = pg_query($conexao, $query);
                $numregs=pg_numrows($result2);
                if($numregs == 0){
                    $pdf->Cell(60,4,'Não foi produzido nada nesse dia',0,0,'L');
                }
                $i=0;
                $pdf->Cell(40,5,"CPF Fuincionário",0,0,'C');
                $pdf->Cell(40,5,"Nome Fuincionário",0,0,'L');
                $pdf->Cell(17,5,"Código",0,0,'C');
                $pdf->Cell(50,5,"Nome Produto",0,0,'L');
                $pdf->Cell(30,5,"Qtd total Produzida",0,1,'L');
            while($i<$numregs)
            {
                $cpf=pg_result($result2,$i,'cpf_funcio');
                $nomeFuncio = pg_result($result2,$i,'nome_funcio');
                $cod=pg_result($result2,$i,'cod_prod');
                $nome=pg_result($result2,$i,'nome_prod');
                $qtd_total=pg_result($result2,$i,'quant_produz');
                $pdf->Cell(40,4,$cpf,0,0,'C');
                $pdf->Cell(40,4,$nomeFuncio,0,0,'L');
                $pdf->Cell(17,4,$cod,0,0,'C');
                $pdf->Cell(60,4,$nome,0,0,'L');
                $pdf->Cell(30,4,$qtd_total,0,1,'L');
                $i++;
            }    
                $pdf->Cell(0,3,'----------------------------------------------------------------------------------------------------------------------------------------------------',0,1);
                $data = date('d-m-Y', strtotime("+1 days",strtotime($data))); 
            }
            $pdf->SetFontSize(18);
            $pdf->Cell(60,15,"Tabela de Produtos produzidos entre $dataInicio e $dataFim ",0,1,'L');
            $pdf->SetFontSize(11);
            $query = "SELECT cpf_funcio, produto.cod_prod, nome_prod, preco_unitario, SUM(produz.quantidade_prod) AS quant_produz FROM produz, produto WHERE produz.cod_prod = produto.cod_prod AND produz.data_producao BETWEEN '$dataInicio' AND '$dataFim' GROUP BY produto.cod_prod, cpf_funcio ORDER BY produto.cod_prod;";
            
            $result2 = pg_query($conexao, $query);
            $numregs=pg_numrows($result2);
            $i=0;
                $pdf->Cell(30,7,"CPF Fuincionário",1,0,'C');
                $pdf->Cell(15,7,"Código",1,0,'C');
                $pdf->Cell(60,7,"Nome",1,0,'L');
                $pdf->Cell(25,7,"Preço Unitário",1,0,'L');
                $pdf->Cell(35,7,"Qtd total Produzida",1,1,'L');
            while($i<$numregs)
            {
                $cpf=pg_result($result2,$i,'cpf_funcio');
                $cod=pg_result($result2,$i,'cod_prod');
                $nome=pg_result($result2,$i,'nome_prod');
                $preco=pg_result($result2,$i,'preco_unitario');
                $qtd_total=pg_result($result2,$i,'quant_produz');
                $preco = money_format('%.2n',$preco);
                $pdf->Cell(30,7,$cpf,1,0,'C');
                $pdf->Cell(15,7,$cod,1,0,'C');
                $pdf->Cell(60,7,$nome,1,0,'L');
                $pdf->Cell(25,7,"R$ $preco" ,1,0,'L');
                $pdf->Cell(35,7,$qtd_total,1,1,'L');
                $i++;
            }    
            $pdf->Output("I", "Produção-$dataInicio-$dataFim.pdf");
        }else{
            $pdf->SetFontSize(18);
            $dataFim = date('d-m-Y', strtotime($dataFim));
            $dataInicio = date('d-m-Y', strtotime($dataInicio));
            $data = date('d-m-Y', strtotime($dataInicio));
            $pdf->Cell(100,10, "Relatório de Geral do dia $data ao dia $dataFim", 0, 1);
            $pdf->SetFontSize(11);
    
            while($data != $dataFim){
                $pdf->Cell(60,7,"Relatório Geral do Dia: $data",0,1,'L');
                $query = "SELECT produto.cod_prod, nome_prod, preco_unitario, SUM(possui.quantidade_possui) AS quantidade FROM possui, produto, pedido WHERE possui.cod_prod = produto.cod_prod AND pedido.cod_pedido = possui.cod_pedido AND pedido.data_venda = '$data' GROUP BY produto.cod_prod ORDER BY produto.cod_prod;";
                $result2 = pg_query($conexao, $query);
                $numregs=pg_numrows($result2);
                if($numregs == 0){
                    $pdf->Cell(60,4,'Não foi produzido nada e não houve Vendas nesse dia',0,0,'L');
                }
               
                $i=0;
                $pdf->Cell(15,5,"Código",0,0,'C');
                $pdf->Cell(50,5,"Nome",0,0,'L');
                $pdf->Cell(30,5,"Qtd Produzida",0,0,'L');
                $pdf->Cell(44,5,"Qtd Vendida",0,0,'L');
                $pdf->Cell(30,5,"Total",0,1,'L');
            while($i<$numregs)
            {
                 $cod=pg_result($result2,$i,'cod_prod');
                 $query2 = "SELECT SUM(produz.quantidade_prod) AS quant_produz FROM produz, produto WHERE produz.cod_prod = '$cod' AND produz.cod_prod = produto.cod_prod AND produz.data_producao = '$data'  GROUP BY produto.cod_prod;";
                $result3 = pg_query($conexao, $query2);
                $nome=pg_result($result2,$i,'nome_prod');
                $preco=pg_result($result2,$i,'preco_unitario');
                $qtd_totalProd=pg_result($result3,0,'quant_produz');
                $qtd_total=pg_result($result2,$i,'quantidade');
                $vendas = $qtd_total * $preco;
                $vendas = money_format('%.2n',$vendas); 
                $pdf->Cell(15,4,$cod,0,0,'C');
                $pdf->Cell(60,4,$nome,0,0,'L');
                $pdf->Cell(25,4,$qtd_totalProd,0,0,'L');
                $pdf->Cell(35,4,$qtd_total,0,0,'L');
                $pdf->Cell(30,4,"R$ $vendas",0,1,'L');
                $i++;
            }    
                $pdf->Cell(0,3,'----------------------------------------------------------------------------------------------------------------------------------------------------',0,1);
                $data = date('d-m-Y', strtotime("+1 days",strtotime($data))); 
            }
            $pdf->SetFontSize(18);
            $pdf->Cell(60,15,"Tabela de Produtos produzidos e Vendidos entre $dataInicio e $dataFim ",0,1,'L');
            $pdf->SetFontSize(11);
            $query = "SELECT produto.cod_prod, nome_prod, preco_unitario, SUM(possui.quantidade_possui) AS quantidade FROM possui, produto, pedido WHERE possui.cod_prod = produto.cod_prod AND pedido.cod_pedido = possui.cod_pedido AND pedido.data_venda BETWEEN '$dataInicio' AND '$dataFim' GROUP BY produto.cod_prod ORDER BY produto.cod_prod;";
            
            $result2 = pg_query($conexao, $query);
            $numregs=pg_numrows($result2);
            $i=0;
                $pdf->Cell(15,7,"Código",1,0,'C');
                $pdf->Cell(60,7,"Nome",1,0,'L');
                $pdf->Cell(25,7,"Qtd Produzida",1,0,'L');
                $pdf->Cell(35,7,"Qtd Vendida",1,0,'L');
                $pdf->Cell(30,7,"Total",1,1,'L');
            while($i<$numregs)
            {
                $cod=pg_result($result2,$i,'cod_prod');
                
                 $query2 = "SELECT SUM(produz.quantidade_prod) AS quant_produz FROM produz, produto WHERE produz.cod_prod = '$cod' AND produz.cod_prod = produto.cod_prod AND produz.data_producao BETWEEN '$dataInicio' AND '$dataFim' GROUP BY produto.cod_prod;";
                $result3 = pg_query($conexao, $query2);
                
                $nome=pg_result($result2,$i,'nome_prod');
                $preco=pg_result($result2,$i,'preco_unitario');
                $qtd_totalProd=pg_result($result3,0,'quant_produz');
                $qtd_total=pg_result($result2,$i,'quantidade');
                $vendas = $qtd_total * $preco;
                $vendas = money_format('%.2n',$vendas); 
                $pdf->Cell(15,7,$cod,1,0,'C');
                $pdf->Cell(60,7,$nome,1,0,'L');
                $pdf->Cell(25,7,$qtd_totalProd,1,0,'L');
                $pdf->Cell(35,7,$qtd_total,1,0,'L');
                $pdf->Cell(30,7,"R$ $vendas",1,1,'L');
                $i++;
            }    
            $pdf->Output("I", "Geral-$dataInicio-$dataFim.pdf");
        }
        
    }
         
    pg_close($conexao);
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Peter Pão: Gerar Relatório</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
       <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/table.css">
        <link rel="stylesheet" href="css/clientes.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <script>        
            window.onload = function(){
                n = window.location.href.search("relatorio=adicionar2");
                if (n > -1) {
                    change("adicionar");
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
                <a href="#" onclick="window.location.replace('estoque.php')"><i class="fa fa-archive"style="font-size:24px; padding-right:10px"></i>   Estoque</a> 
                </div>
                <?php if($_SESSION['gerente'] == 1){?>
                <div class="vertical-menuGerente">
                <a href="#" onclick="window.location.replace('funcionarios.php')"> <i class="fa fa-user" style="font-size:24px; padding-right:10px"></i>   Funcionários </a>
                <a href="#"   class="active" onclick="window.location.replace('relatorio.php')"><i class="fa fa-clipboard"style="font-size:24px; padding-right:10px"></i>  Gerar Relatório</a>
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
                   document.getElementById("adicionar2").style.display = 'none';
                   document.getElementById("alterar").style.display = 'none';
                   document.getElementById("consul").className = "botao active";
                   document.getElementById("add").className = "botao";
                   document.getElementById("alt").className = "botao";
                   
                }else if(x === "adicionar"){
                    document.getElementById("adicionar2").style.display = 'block';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("alterar").style.display = 'none';
                    document.getElementById("consul").className = "botao";
                    document.getElementById("alt").className = "botao";
                   document.getElementById("add").className = "botao active";
                }else if(x === "alterar"){
                    document.getElementById("alterar").style.display = 'block';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("adicionar2").style.display = 'none';
                    document.getElementById("consul").className = "botao";
                   document.getElementById("alt").className = "botao active";
                    document.getElementById("add").className = "botao";
                }
                
            }
            function alter_url(obj){
                str = "?relatorio=" + obj;
                window.location.replace(window.location.href + str);
            }
            function alter_tipo(x){
                if(x === "vendas"){
                    document.getElementById('tipoRela').value="vendas";
                }else if(x === "producao"){
                    document.getElementById('tipoRela').value="producao";    
                }else{
                    document.getElementById('tipoRela').value="geral";
                }
                
                 document.getElementById('rpdfRelatorio').submit();
            }
            </script>
        </div>
            <div class="col-md-9  gambi">
                <div class="container" style="background-image: url('Img/report.png');">
                <h2>Gerar Relatório</h2>
                    <div class="conteudo">
                       <table id="menuTabela">
                            <tr>
                                <td id="consul" class="botao active"> <a href="#" onclick="change('consultar')" style="text-align: center"> Produto por venda</a></td>
                                <td rowspan="7" style = "width: 100%"> 
                                    <div id="consultar">
                                        <h3> Quantidade Vendida no Intervalo:</h3>
                                        <form class="busca" style="white-space: nowrap"  method="post">
                                        <input type="hidden" name="tipoBusca" value="vendas">
                                          <input type="text" placeholder="buscar produto por.." maxlength="50" name="search" required>
                                          <select id="buscaOp" name="buscaOp">
                                          <option >Código</option>
                                          <option >Nome</option>
                                          <option >Categoria</option>
                                        </select>
                                        <label for="pordataDe" class="titulo"> De:</label>    
                                        <input type="date" placeholder="dd/mm/aaaa" name="pordata" required>
                                        <label for="pordataate" class="titulo"> Até:</label>    
                                        <input type="date" placeholder="dd/mm/aaaa" name="pordataate" required>
                                        <button type="submit" class="treco">Buscar</button>      
                                        </form>
                                        <table id="tabelaConsultar">
                                            <thead>
                                              <tr>
                                                <th>Código</th>
                                                <th>Nome</th>
                                                <th>Quantidade no Estoque</th>
                                                <th>Categoria</th>
                                                <th>Descrição</th>  
                                                <th>Preço Unitário</th>
                                                <th>Quantidade Vendida</th>  
                                              </tr>
                                            
                                            </thead>
                                            <tbody>
                                              <?php
                                                     while($obj = pg_fetch_object($result))
                                                    {        ?>
                                                    <tr>
                                                    <td><?php echo $obj->cod_prod;?></td>
                                                    <td><?php echo $obj->nome_prod;?></td>    
                                                    <td><?php echo $obj->quantidade_estoque; ?></td>
                                                    <td><?php echo $obj->categoria; ?></td>
                                                    <td><?php echo $obj->descricao; ?></td>
                                                   <td><?php echo "R$ " . money_format('%.2n', $obj->preco_unitario); ?></td> 
                                                    <td><?php echo $obj->quant_pedidos; ?></td>    
                                                    <?php         
                                                    }
                                                    ?>         
                                            
                                            </tbody>
                                        </table>
                                    </div>
                                     <div id="adicionar2">
                                        <h3> Quantidade Produzida no Intervalo:</h3>
                                        <form class="busca">
                                            <input type="hidden" name="tipoBusca2" value="produz">
                                          <input type="text" placeholder="buscar produto por.." maxlength="50" name="search2" required>
                                          <select id="buscaOp" name="buscaOp2">
                                          <option >Código</option>
                                          <option >Nome</option>
                                          <option >Categoria</option>
                                        </select>
                                        <label for="pordataDe" class="titulo"> De:</label>    
                                        <input type="date" placeholder="dd/mm/aaaa" name="pordata2">
                                        <label for="pordataate" class="titulo"> Até:</label>    
                                        <input type="date" placeholder="dd/mm/aaaa" name="pordataate2">
                                        <button type="submit" class="treco" onclick="alter_url('adicionar2')">Buscar</button>    
                                        </form>
                                        <table id="tabelaProducao">
                                            <thead>
                                              <tr>
                                                <th>Código</th>
                                                <th>Nome</th>
                                                <th>Quantidade no Estoque</th>
                                                <th>Categoria</th>
                                                <th>Descrição</th>  
                                                <th>Preço Unitário</th>
                                                <th>Quantidade Produzida</th>  
                                              </tr>
                                            </thead>
                                            <tbody>
                                               <?php
                                                     while($obj = pg_fetch_object($result2))
                                                    {        ?>
                                                    <tr>
                                                    <td><?php echo $obj->cod_prod;?></td>
                                                    <td><?php echo $obj->nome_prod;?></td>    
                                                    <td><?php echo $obj->quantidade_estoque; ?></td>
                                                    <td><?php echo $obj->categoria; ?></td>
                                                    <td><?php echo $obj->descricao; ?></td>
                                                    <td><?php echo "R$ " . money_format('%.2n', $obj->preco_unitario); ?></td> 
                                                    <td><?php echo $obj->quant_produz; ?></td>    
                                                    <?php         
                                                    }
                                                    ?>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="alterar">
                                        <h3>Gerar Ralatórios do Intervalo: </h3>
                                            <table id="relatorio">
                                            <form name="rpdfRelatorio" id="rpdfRelatorio" method="get" target="_blank">
                                            <tr>
                                                <td> 
                                                    <label for="pordataDe" class="titulo"> De:</label>    
                                                    <input type="date" placeholder="dd/mm/aaaa" name="pordata3">
                                                </td>
                                                <td>
                                                    <label for="pordataate" class="titulo"> Até:</label>    
                                                    <input type="date" placeholder="dd/mm/aaaa" name="pordataate3">
                                                </td>
                                        </tr>
                                        <tr>
                                            <input type="hidden" id="tipoRela" name="tipoRelatorio" value="none">
                                            <td><button type="button" class="botaoRelatorio" onclick="alter_tipo('vendas')">Relatório de Vendas <i class="material-icons" style="font-size:24px; vertical-align: bottom; margin: 0 0 0 10px">picture_as_pdf</i></button></td> 
                                            <td><button type="button" class="botaoRelatorio" onclick="alter_tipo('producao')">Relatório de Produção <i class="material-icons" style="font-size:24px; vertical-align: bottom; margin: 0 0 0 10px">picture_as_pdf</i></button></td> 
                                        </tr>
                                        <tr> 
                                            <td colspan="2"><button type="button" class="botaoRelatorio" style="background-color: #ff8600; font-size: 15pt" onclick="alter_tipo('geral')"><b>Relatório Geral </b><i class="material-icons" style="font-size:24px; vertical-align: bottom; margin: 0 0 0 10px">picture_as_pdf</i></button></td>       
                                        </tr> 
                                    </form>            
                                        </table>
                                    </div>
                                </td>
                           </tr>
                           <tr>
                                <td id="add" class="botao"> <a href="#" onclick="change('adicionar')" style="text-align: center"> Produto por Produção</a> </td>
                           </tr>
                           <tr>
                               <td id="alt"class="botao"> <a href="#" onclick="change('alterar')" style="text-align: center"> Gerar Relatórios </a></td>
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