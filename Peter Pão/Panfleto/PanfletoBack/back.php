<?php
if(!@($conexao=pg_connect ("host=localhost dbname=Padaria port=5432 user=postgres password=1234"))) {
   print "Não foi possível estabelecer uma conexão com o banco de dados.";
} 
?>

<!DOCTYPE html>

<html>
<html lang="pt-BR">
<meta charset="utf-8">
<title>Peter Pão</title>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="panfleto.css" rel="stylesheet">
    <link href="panfleto.js">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="css/style.css" rel="stylesheet">

</head>
<body>

<nav id = "nb" class="navbar navbar-default navbar">
  <div id = "container-navbar" class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
        <a class = "navbar-brand">
            <h3>Peter Pão <img src="paoIcon.png" class="logo" width="60">
            </h3>
        </a>
    </div>
   <div class="collapse navbar-collapse" id="myNavbar">
    <ul class="nav navbar-nav navbar-right">
        <li><a href="panfleto.php">Home</a></li>
        <li><a href="historia.php">Nossa História</a></li>
        <li class=active><a href="produto.php">Nossos Produtos</a></li>
        <li><a href="chegar.php">Como Chegar</a></li>
        <li><a href="fale.php">Fale Conosco</a></li>
      </ul>
    </div>
  </div>
</nav>

<div id="container-prod" class="container">
  <h2>Busque por nossos produtos!</h2>
  <p>A Peter Pão tem uma variedade enorme de produtos!</p>
  <form action = "#" method="post" class="form-inline">
    <div class="form-group">
      <label id = "prod" for="text">Produto:</label>
      <input name="nome_produto" type="text" class="form-control" id="nome_prod" placeholder="Digite o nome do produto" autofocus>
    </div>
  <label for="sel1">Categoria:</label>
  <select name = "opcao" class="form-control" id="cat">
    <option>Todas</option>
    <option>Pães</option>
    <option>Tortas</option>
    <option>Doces</option>
    <option>Bolos</option>
    <option>Salgados</option>
  </select>
      <label for="sel1">Ordenar por:</label>
      <select name = "ordem" class="form-control" id="ord">
        <option>Menor preço</option>
        <option>Maior preço</option>
      </select>
    <button name="submit" type="submit" class="btn btn-success">Buscar</button>
  </form>
</div>

<div id="table-prod" class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Nome do Produto</th>
                <th>Descrição</th>
                <th>Preço Unitário/Unidade</th>
              </tr>
            </thead>
            <tbody>
            <?php
                if (isset($_POST['submit'])) {
                    $categoria = htmlspecialchars($_POST["opcao"]);
                    $ordem = htmlspecialchars($_POST["ordem"]);
                    $nome_produto = htmlspecialchars($_POST["nome_produto"]);
                    if ($nome_produto != null) {
                        echo $nome_produto;
                        if (strcmp($categoria, "Todas") == 0) {
                            $query = "SELECT nome_prod, preco_unitario, descricao FROM produto WHERE nome_prod LIKE '$nome_produto%' AND categoria != 'Mercearia' AND categoria != 'Ingrediente' AND categoria != 'Bebidas'";     
                        } else {
                             $query = "SELECT nome_prod, preco_unitario, descricao FROM produto WHERE nome_prod LIKE '$nome_produto%' AND categoria = '$categoria'";   
                        }
                    } else {
                        if (strcmp($categoria, "Todas") == 0) {
                            if (strcmp($ordem, "Menor preço") == 0) {
                                $query = "SELECT nome_prod, preco_unitario, descricao FROM produto WHERE categoria != 'Mercearia' AND categoria != 'Ingrediente' AND categoria != 'Bebidas' ORDER BY preco_unitario";
                            } else {
                                $query = "SELECT nome_prod, preco_unitario, descricao FROM produto WHERE categoria != 'Mercearia' AND categoria != 'Ingrediente' AND categoria != 'Bebidas' ORDER BY preco_unitario DESC";
                            }
                        } else {
                             if (strcmp($ordem, "Menor preço") == 0) {
                                $query = "SELECT nome_prod, preco_unitario, descricao FROM produto WHERE categoria = '$categoria' ORDER BY preco_unitario";
                            } else {
                                $query = "SELECT nome_prod, preco_unitario, descricao FROM produto WHERE categoria = '$categoria' ORDER BY preco_unitario DESC";
                            }
                        }
                    }
                   
                    $result = pg_query($conexao, $query);
                    while($linha = pg_fetch_array($result)) {    
                        $nome = $linha['nome_prod'];
                        $descricao = $linha['descricao'];
                        $preco = $linha['preco_unitario'];
                        echo "<tr><td>".$nome."</td><td>".$descricao."</td><td>".$preco."</td></tr>";
                    }
                    pg_close($conexao);
                }
            ?>
            </tbody>
          </table>
</div>
    
  <!--Footer-->
  <footer class="page-footer">
      <div id="container-footer" class="container-fluid text-center">
          <div class="row">
                  <img class=logo src="paoIcon.png" width="60" alt="logo">
                  <p><b>Unidade 1:</b> Rua J. M. Barrie, 42. Neverland.
                  <br/>
                      <b>Unidade 2:</b> Rua The Little White Bird, 12. Disneyland.
                  <br/>
                      <b>Unidade 3:</b> Rua The Boy Who Wouldn’t Grow, 132. Pirate Ship.
                  <br/>
                      <b>Unidade 4:</b> Avenida Once Upon a Time, 1289. Dreamland.
                  <br/>
                      <b>Unidade 5:</b>  Kensington Gardens, 901. Orlando.
                  </p>
          </div>
      </div>
  </footer>
</body>
</html>
