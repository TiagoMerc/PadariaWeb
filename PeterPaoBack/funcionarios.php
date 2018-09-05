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


    if (isset($_GET["search"])) {
        $consulta = $_GET["search"];
        $opcao = $_GET["buscaOp"]; 

        if (strcmp($opcao, "Nome") == 0) {
            $query = "SELECT cpf_funcio, nome_funcio, salario, cargo FROM funcionario WHERE nome_funcio ILIKE '$consulta%'";
        } else if (strcmp($opcao, "CPF") == 0) {
            $consulta = str_replace(".", "", $consulta);
            $consulta = str_replace("-", "", $consulta);
            $query = "SELECT cpf_funcio, nome_funcio, salario, cargo FROM funcionario WHERE cpf_funcio = '$consulta'";
        } else {
            $query = "SELECT cpf_funcio, nome_funcio, salario, cargo FROM funcionario WHERE cargo ILIKE '$consulta'";
        }
        
        $result = pg_query($conexao, $query);
    
    }
      
    if(isset($_POST['cadastro'])) {
       if($_POST['cadastro'] == "funcionario") {// se é o cadastro de funcionarios     
            $cpf = $_POST['cpf_ins'];   
            $nome = $_POST['nome_ins'];
            $salario = $_POST['salario_ins'];
            $cargo = $_POST['cargo_ins'];
            $senha = $_POST['senha_ins'];
            $cpf = str_replace(".", "", $cpf);
            $cpf = str_replace("-", "", $cpf);
           
            $insert = "INSERT INTO funcionario(
            cpf_funcio, nome_funcio, salario, cargo, senha)
            VALUES ('$cpf', '$nome', $salario, '$cargo', md5('$senha'));";

            $res = pg_exec($conexao, $insert); 
            pg_query($conexao, $sql); 
           
            //"$qtd_linhas" recebe a quantidade de Linhas Afetadas pela Inserção 
            $qtd_linhas = pg_affected_rows($res); 
                                               
             if ($qtd_linhas == 0) { 
                echo '<script language="javascript"> alert("Não foi possível inserir o funcionário. Verique se o cpf já está cadastrado no sistema!") </script>';
            } else {
                echo '<script language="javascript"> alert("Funcionario inserido com sucesso!") </script>';
                echo '<script language="javascript"> location.href="funcionarios.php" </script>';
            }
       }   
    }

     if (isset($_GET['funcionario'])) {
          $cpf_old = $_GET['funcionario'];
          $query = "SELECT cpf_funcio, nome_funcio, salario, cargo FROM funcionario WHERE cpf_funcio = '$cpf_old'";
          $dados = pg_query($conexao, $query);
          $func = pg_fetch_object($dados);
          $nome_func = $func->nome_funcio;
          $cpf_func = $func->cpf_funcio;
          $salario_func = $func->salario;
          $cargo_func = $func->cargo;
          $cpf_func = mask($cpf_func,'###.###.###-##');
          
     }

    if(isset($_POST['confirmar'])) {
        if($_POST['atualizar'] == "funcionario") {
            if (isset($_GET['funcionario'])) {
                $cpf_old = $_GET['funcionario'];
                $cpf = $_POST['cpf_up'];   
                $nome = $_POST['nome_up'];
                $salario = $_POST['salario_up'];
                $cargo = $_POST['cargo_up'];
                $senha = $_POST['senha_up'];
                $cpf = str_replace(".", "", $cpf);
                $cpf = str_replace("-", "", $cpf);
                
                $update = " UPDATE public.funcionario
                            SET cpf_funcio='$cpf', nome_funcio='$nome', salario=$salario, cargo='$cargo', senha=md5('$senha')
                            WHERE cpf_funcio = '$cpf_old';";
                
                $res = pg_exec($conexao, $update); 
                pg_query($conexao, $sql); 

                $qtd_linhas = pg_affected_rows($res); 
                
                if ($qtd_linhas == 0) { 
                    echo '<script language="javascript"> alert("Não foi possível alterar o funcionário. Verique se o cpf inserido é válido!") </script>';
                    echo '<script language="javascript"> location.href="funcionario.php" </script>';
                } else {
                    echo '<script language="javascript"> alert("Funcionário alterado com sucesso!") </script>';
                    echo '<script language="javascript"> location.href="funcionarios.php" </script>';
                }

               
            }
        }
    }

    pg_close($conexao);
?>        

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Peter Pão: Funcionários</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
       <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/table.css">
        <link rel="stylesheet" href="css/clientes.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>        
            window.onload = function(){
                n = window.location.href.search("funcionario=");
                if (n > -1) {
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
                    <td style="width: 30%"><img src="./Img/paoIcon.png" alt="logo" class="logo"></td>
                    <td> <h3> Peter Pão </h3> </td>
                </tr>
               </table>
             <div class="vertical-menu">
                <a href="home.php" onclick="window.location.replace('home.php')"> <i class="fa fa-home" style="font-size:24px; padding-right:10px" ></i> Home </a>
                <a href="clientes.php" onclick="window.location.replace('clientes.php')"> <i class="fa fa-users" style="font-size:24px; padding-right:10px"></i>   Clientes </a>
                <a href="encomendas.php" onclick="window.location.replace('encomendas.php')"><i class="fa fa-book"style="font-size:24px; padding-right:10px"></i>  Encomendas</a>
                <a href="vendas.php" onclick="window.location.replace('vendas.php')"><i class="fa fa-shopping-basket"style="font-size:24px; padding-right:10px"></i>  Vendas </a> 
                <a href="estoque.php" onclick="window.location.replace('estoque.php')"><i class="fa fa-archive"style="font-size:24px; padding-right:10px"></i>   Estoque</a> 
                </div>
                <?php if($_SESSION['gerente'] == 1){?>
                <div class="vertical-menuGerente">
                <a href="funcionarios.php" class="active" onclick="window.location.replace('funcionarios.php')"> <i class="fa fa-user" style="font-size:24px; padding-right:10px"></i>   Funcionários </a>
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
        <script>
            function change(x){
               if(x === "consultar"){
                   window.history.pushState(null, null, "funcionarios.php");
                   document.getElementById("consultar").style.display = 'block';
                   document.getElementById("adicionar").style.display = 'none';
                   document.getElementById("alterar").style.display = 'none';
                   document.getElementById("consul").className = "botao active";
                   document.getElementById("add").className = "botao";
                }else if(x === "adicionar"){
                    window.history.pushState(null, null, "funcionarios.php");
                    document.getElementById("adicionar").style.display = 'block';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("alterar").style.display = 'none';
                    document.getElementById("consul").className = "botao";
                   document.getElementById("add").className = "botao active";
                }else if(x === "alterar"){
                    document.getElementById("alterar").style.display = 'block';
                    document.getElementById("consultar").style.display = 'none';
                    document.getElementById("adicionar").style.display = 'none';
                }
            }
            </script>
        </div>
            <div class="col-md-9  gambi">
                <div class="container" style="background-image: url('Img/chef.png');">
                <h2>Funcionários</h2>
                    <div class="conteudo">
                       <table id="menuTabela">
                            <tr>
                                <td id="consul" class="botao active"> <a onclick="change('consultar')"> Consultar</a></td>
                                <td rowspan="7" style = "width: 100%"> 
                                    <div id="consultar">
                                        <div class="busca">
                                        <form name="consultar" method="get" >    
                                          <input type="text" placeholder="buscar por cpf.." maxlength="14" name="search" oninput="cpf_change()" pattern="\d{3}.\d{3}.\d{3}-\d{2}" title="Apenas números. 11 no total." required>
                                          <select id="buscaOp" name="buscaOp" onchange="muda_buscaOp()">
                                          <option>CPF</option>
                                          <option>Nome</option>
                                          <option>Cargo</option>
                                        </select>
                                        </form>
                                        </div>
                                        <table id="tabelaConsultar">
                                            <thead>
                                            <tr>
                                            <th>CPF</th>
                                            <th>Nome</th>
                                            <th>Cargo</th>
                                            <th>Salário</th>  
                                            <th></th>
                                             </tr>
                                            
                                            </thead>
                                            <tbody>
                                
                                             <?php
                                                 while($obj = pg_fetch_object($result))
                                                {        ?>
                                                <tr>
                                                <td><?php echo mask($obj->cpf_funcio, '###.###.###-##'); ?></td>
                                                <td><?php echo $obj->nome_funcio; ?></td>
                                                <td><?php echo $obj->cargo; ?></td>
                                                <td><?php $obj->salario = "R$ " . money_format('%.2n', $obj->salario);echo $obj->salario; ?>  </td> 
                                                <td>

                                                    <button class= "edit" type="edit" onclick="location.href= 'funcionarios.php?funcionario=<?php echo $obj->cpf_funcio ?>'" >
                                                    <i class="material-icons" style="color:white; font-size: 10pt">edit</i>
                                                    </button>
                                               
                                                </td>
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
                                                        <label for="cpf" class="titulo"><b>    CPF </b></label>
                                                        <input type="text" name="cpf_ins" id="cpf_add" oninput="cpf_replace(this.form.cpf_add.value,'cpf_add')" placeholder="xxx.xxx.xxx-xx" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="CPF tem 11 dígitos (Digite apenas números)" maxlenght="15" required autofocus >
                                                        </td>
                                                    <td>
                                                        <label for="cargo" class="titulo"> <b>   Cargo    </b></label>
                                                        <select id="setCargo" name="cargo_ins">
                                                          <option>Vendedor</option>
                                                          <option>Padeiro</option>
                                                          <option>Gerente</option>
                                                        </select>
                                                    </td>    
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td>
                                                         <label for="nome" class="titulo"><b>  Nome  </b></label>
                                                        <input type="text" placeholder="Nome" maxlength="50" name="nome_ins" required>
                                                         </td>
                                                         <td>
                                                             <label for="salario" class="titulo"> <b>  Salário    </b></label>
                                                             <input name="salario_ins" type="number" step="0.01" placeholder="xxxx.xx" required>                       
                                                         </td>
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td>
                                                            <label for="senha" class="titulo"> <b>  Senha  </b></label>
                                                            <input type="password" placeholder="senha" required name="senha_ins">
                                                         </td>
                                                     </tr>
                                                 </table>
                                              </div>
                                            <div class="botoes">
                                                <button type="reset" class="cancelbtn">Limpar</button>
                                                <button type="submit">Adicionar</button>
                                            </div>
                                            <input type="hidden" name="cadastro" value="funcionario">
                                        </form>
                                    </div>
                                    <div id="alterar">
                                        <h3>Alterar Dados</h3>
                                        <form name="alterar" method="post">
                                             <div class="containerBox">
                                                 <table id="formulario">
                                                     <tr>
                                                     <td>
                                                        <label for="cpf" class="titulo"><b>    CPF </b></label>
                                                        <input type="text"  id="cpf_alt" name="cpf_up" oninput="cpf_replace(this.form.cpf_alt.value,'cpf_alt')" placeholder="xxx.xxx.xxx-xx" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="CPF tem 11 dígitos (Digite apenas números)" maxlenght="15" required autofocus value="<?php echo $cpf_func; ?>">
                                                        </td>
                                                    <td>
                                                        <label for="cargo" class="titulo"> <b> Cargo </b></label>
                                                        <select id="setCargo" name="cargo_up">
                                                          <option <?php if (strcmp($cargo_func, "Vendedor") == 0) {  echo 'selected = "selected"';  } ?>> Vendedor</option>
                                                          <option <?php if (strcmp($cargo_func, "Padeiro") == 0) {  echo 'selected = "selected"';  } ?>>Padeiro</option>
                                                          <option <?php if (strcmp($cargo_func, "Gerente") == 0) {  echo 'selected = "selected"';  } ?>>Gerente</option>
                                                        </select>
                                                    </td>    
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td>
                                                         <label for="nome" class="titulo"><b>  Nome  </b></label>
                                                        <input type="text" placeholder="Nome" maxlength="50" name="nome_up" required value="<?php echo $nome_func; ?>">
                                                         </td>
                                                         <td>
                                                             <label for="salario" class="titulo"> <b>  Salário    </b></label>
                                                             <input type="number" placeholder="xxxx,xx" step="0.01" required name="salario_up" value="<?php echo $salario_func; ?>">              
                                                         </td>
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td>
                                                            <label for="senha" class="titulo"> <b>  Senha  </b></label>
                                                            <input type="password" placeholder="senha" name="senha_up" maxlength="8" required>
                                                         </td>
                                                     </tr>
                                                 </table>
                                              </div>
                                            <div class="botoes">
                                                <button type="button" class="voltar" onclick="window.history.back();"><i class="material-icons" style="color:white; font-size: 30pt">keyboard_return</i></button>
                                                <button name="confirmar" type="submit">Confirmar</button>
                                            </div>
                                             <input type="hidden" name="atualizar" value="funcionario">
                                        </form>
                                    </div>
                                </td>
                           <tr>
                                <td id="add" class="botao"> <a onclick="change('adicionar')"> Adicionar</a></td>
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
    </body>
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
    
    function tel_replace(pTel, id_form){
        var tel = pTel.toString()

        tel = tel.replace(/[\W\s\._\-]+/g, ''); //para retirar caracteres especiais
        tel = tel.replace(/[A-z.]+/, ''); //para retirar letras

        //vetor que recebera cada parte do telefone
        var tokens = [];

        //tamanho atual do input
        var tamanho = tel.length;
        
        if(tamanho > 2){
            var ddd = tel.substr(0, 2); //ddd
            var parte; //define o tamanho da parte para número de 8 ou 9 dígitos
            
            if(tamanho < 11){
                parte = 4;
            }else{
                parte = 5;
            }
            
            //retirar cada parte do telefone
            for(var i = 2; (i < tamanho) && (i < 10); i+= parte){
                tokens.push(tel.substr(i, parte));
                if (parte == 5)
                    i++;
                parte = 4;
            }
            
            //junção
            tel = "(" + ddd + ")" + tokens.join("-");
        }

        //substitui no input
        document.getElementById(id_form).value = tel
    }
    
    function cep_replace(pCep, id_form){
        var cep = pCep.toString()

        cep = cep.replace(/[\W\s\._\-]+/g, ''); //para retirar caracteres especiais
        cep = cep.replace(/[A-z.]+/, ''); //para retirar letras

        //tamanho atual do input
        var tamanho = cep.length;
        
        //tratamento cep
        if(tamanho > 5){
            var tokens = [];
            tokens.push(cep.substr(0, 5));
            tokens.push(cep.substr(5, 3));
            cep = tokens.join("-");
        }

        //substitui no input
        document.getElementById(id_form).value = cep
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
    
    function muda_buscaOp(){
        var opcao = document.getElementsByName("buscaOp")[0].value;
       
        if(opcao.localeCompare("CPF") == 0){
            $("input[name='search']").replaceWith("<input type=\"text\" placeholder=\"buscar por cpf..\" maxlength=\"14\" name=\"search\" oninput=\"cpf_change()\" pattern=\"\\d{3}.\\d{3}.\\d{3}-\\d{2}\" title=\"Apenas números. 11 no total.\" required>");
            $("select[name='search']").replaceWith("<input type=\"text\" placeholder=\"buscar por cpf..\" maxlength=\"14\" name=\"search\" oninput=\"cpf_change()\" pattern=\"\\d{3}.\\d{3}.\\d{3}-\\d{2}\" title=\"Apenas números. 11 no total.\" required>");
        }else if(opcao.localeCompare("Nome") == 0){
            $("input[name='search']").replaceWith("<input type=\"text\" placeholder=\"busca por nome..\" maxlength=\"50\" name=\"search\"  title=\"Apenas letras\" required>");
            $("select[name='search']").replaceWith("<input type=\"text\" placeholder=\"busca por nome..\" maxlength=\"50\" name=\"search\"  title=\"Apenas letras\" required>");
        }else if(opcao.localeCompare("Cargo") == 0){
            $("input[name='search']").replaceWith("<select name= \"search\" id=\"buscaOp\" onchange=\"manda_busca()\"><option value=\"\">Selecione...</option> <option value=\"Vendedor\">Vendedor</option> <option value=\"Padeiro\">Padeiro</option> <option value=\"Gerente\">Gerente</option> </select> ");
        }
    }
    
    function manda_busca(){
        var busca = document.getElementsByName("search")[0].value;
        
        location.href="funcionarios.php?search=" + busca + "&buscaOp=Cargo";
    }
</script>