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
            $query = "SELECT cpf_cliente, nome_cliente, rua, numero, infrator, telefone_resi, telefone_cel FROM cliente WHERE nome_cliente = '$consulta'";
        } else if (strcmp($opcao, "CPF") == 0) {
            $query = "SELECT cpf_cliente, nome_cliente, rua, numero, infrator, telefone_resi, telefone_cel FROM cliente WHERE cpf_cliente = '$consulta'";
        } else {
            $query = "SELECT cpf_cliente, nome_cliente, rua, numero, infrator, telefone_resi, telefone_cel FROM cliente WHERE infrator = '1'";
        }   
        $result = pg_query($conexao, $query);
    
        
    }
      
    if(isset($_POST['cadastro'])) {
       if($_POST['cadastro'] == "cliente") {// se é o cadastro de clientes		      
            $cpf = $_POST['cpf_ins'];   
            $nome = $_POST['nome_ins'];
            $cep = $_POST['cep_ins'];
            $rua = $_POST['rua_ins'];
            $numero = $_POST['numero_ins'];
            $tel_res = $_POST['tel_res_ins'];
            $cel = $_POST['tel_cel_ins']; 
            $cpf = str_replace(".", "", $cpf);
            $cpf = str_replace("-", "", $cpf);
            $cep = str_replace("-", "", $cep);
            
            $insert = "INSERT INTO public.cliente(
            cpf_cliente, nome_cliente, rua, numero, cep, telefone_cel, telefone_resi, 
            infrator) VALUES ('$cpf', '$nome', '$rua', $numero, '$cep', '$cel', '$tel_res', 
            '0');";
            
            $res = pg_exec($conexao, $insert); 
            pg_query($conexao, $sql); 
           
            //"$qtd_linhas" recebe a quantidade de Linhas Afetadas pela Inserção 
            $qtd_linhas = pg_affected_rows($res); 
                                               
             if ($qtd_linhas == 0) { 
                echo '<script language="javascript"> alert("Não foi possível inserir o cliente. Verique se o cpf já está cadastrado no sistema!") </script>';
                echo '<script language="javascript"> location.href="clientes.php" </script>';
            } else {
                echo '<script language="javascript"> alert("Cliente inserido com sucesso!") </script>';
                echo '<script language="javascript"> location.href="clientes.php" </script>';
            }
       }   
    }

      if (isset($_GET['cliente'])) {

          $cpf_old = $_GET['cliente'];
          $query = "SELECT cpf_cliente, nome_cliente, rua, numero, cep, telefone_resi, telefone_cel FROM cliente WHERE cpf_cliente = '$cpf_old'";
          $dados = pg_query($conexao, $query);
      $cli = pg_fetch_object($dados);
          $nome_cliente = $cli->nome_cliente;
          $cpf_cliente = $cli->cpf_cliente;
          $rua_cliente = $cli->rua;
          $numero_cliente = $cli->numero;
          $cep_cliente = $cli->cep;
          $res_cliente = $cli->telefone_resi;
          $cel_cliente = $cli->telefone_cel;
          $cpf_cliente = mask($cpf_cliente,'###.###.###-##');
          $cep_cliente = mask($cep_cliente,'#####-###');
    }

    if(isset($_POST['confirmar'])) {
        if($_POST['atualizar'] == "cliente") {
            if (isset($_GET['cliente'])) {
                $cpf_old = $_GET['cliente'];
                $cpf = $_POST['cpf_up'];   
                $nome = $_POST['nome_up'];
                $cep = $_POST['cep_up'];
                $rua = $_POST['rua_up'];
                $numero = $_POST['numero_up'];
                $tel_res = $_POST['tel_res_up'];
                $cel = $_POST['tel_cel_up'];
                $cpf = str_replace(".", "", $cpf);
                $cpf = str_replace("-", "", $cpf);
                $cep = str_replace("-", "", $cep);
                $tel_res = str_replace("-", "", $tel_res);
                $cel = str_replace("-", "", $cel);
                
                $update = "UPDATE cliente
                SET cpf_cliente='$cpf', nome_cliente='$nome', rua='$rua', numero=$numero, cep='$cep', telefone_cel='$cel', 
                telefone_resi='$tel_res'
                WHERE cpf_cliente = '$cpf_old';";
                
                $res = pg_exec($conexao, $update); 
                pg_query($conexao, $sql); 

                $qtd_linhas = pg_affected_rows($res); 
                
                if ($qtd_linhas == 0) { 
                    echo '<script language="javascript"> alert("Não foi possível alterar o cliente. Verique se o cpf inserido é válido!") </script>';
                    echo '<script language="javascript"> location.href="clientes.php" </script>';
                } else {
                    echo '<script language="javascript"> alert("Cliente alterado com sucesso!") </script>';
                    echo '<script language="javascript"> location.href="clientes.php" </script>';
                }

               
            }
        }
    }


    pg_close($conexao);
?>                                          

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Peter Pão: Clientes</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
       <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <script src="alert/dist/sweetalert-dev.js"></script>
        <link rel="stylesheet" href="alert/dist/sweetalert.css">
        <link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/table.css">
        <link rel="stylesheet" href="css/clientes.css">
         <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>        
            window.onload = function(){
                n = window.location.href.search("cliente=");
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
                    <td style="width: 30%"><img src="Img/paoIcon.png" alt="logo" class="logo"></td>
                    <td> <h3> Peter Pão </h3> </td>
                </tr>
               </table>
             <div class="vertical-menu">
                <a href="#" onclick="window.location.replace('home.php')"> <i class="fa fa-home" style="font-size:24px; padding-right:10px" ></i> Home </a>
                <a href="#" class ="active"> <i class="fa fa-users" style="font-size:24px; padding-right:10px"></i>   Clientes </a>
                <a href="#" onclick="window.location.replace('encomendas.php')"><i class="fa fa-book"style="font-size:24px; padding-right:10px"></i>  Encomendas</a>
                <a href="#" onclick="window.location.replace('vendas.php')"><i class="fa fa-shopping-basket"style="font-size:24px; padding-right:10px"></i>  Vendas</a> 
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
        <script>
            function change(x){
               if(x === "consultar"){
                   document.getElementById("consultar").style.display = 'block';
                   document.getElementById("adicionar").style.display = 'none';
                   document.getElementById("alterar").style.display = 'none';
                   document.getElementById("consul").className = "botao active";
                   document.getElementById("add").className = "botao";
                }else if(x === "adicionar"){
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
            
            function alter_url(obj){
                str = "?cliente=" + obj;
                window.location.replace(window.location.href + str);
            }
            
            
         
            </script>
            
        </div>
            <div class="col-md-9  gambi">
                <div class="container" style="background-image: url('Img/bread.png');">
                <h2>Clientes</h2>
                    <div class="conteudo">
                       <table id="menuTabela">
                            <tr>
                                <td id="consul" class="botao active"> <a name="consultar" onclick="change('consultar')"> Consultar</a></td>
                                <td rowspan="7" style = "width: 100%"> 
                                    <div id="consultar">
                                        <div class="busca">
                                            <form name = "consultar" method="get" action="clientes.php">
                                            <input type="text" placeholder="buscar por.." maxlength="50" name="search">
                                            <select id="buscaOp" name="buscaOp">
                                              <option>CPF</option>
                                              <option>Nome</option>
                                              <option>Infrator</option>
                                            </select>
                                            </form>
                                        </div>
                                        <table id="tabelaConsultar">
                                            <thead>
                                            <tr>
                                                <th>CPF</th>
                                                <th>Nome</th>
                                                <th>Endereço</th>
                                                <th>Residencial</th> 
                                                <th>Celular</th> 
                                                <th>Infrator</th>
                                                <th></th> 
                                              </tr>
                                            
                                            </thead>
                                            <tbody>
                                                <?php
                                                 while($obj = pg_fetch_object($result))
                                                {        ?>
                                                <tr>
                                                <td><?php echo $obj->cpf_cliente;?></td>
                                                <td style="text-align:left; text-indent: 17px;"><?php echo $obj->nome_cliente; ?></td>
                                                <td style="text-align:left; text-indent: 17px;"><?php echo $obj->rua.", ".$obj->numero; ?></td>
                                                <td><?php if ($obj->telefone_resi != null) { echo $obj->telefone_resi; } ?></td> 
                                                <td><?php if ($obj->telefone_cel != null) { echo $obj->telefone_cel; } ?></td>     
                                                <td><?php echo $obj->infrator; ?>  </td> 
                                                <td>

                                                   <button class= "edit" type="edit" onclick="location.href='clientes.php?cliente=<?php echo $obj->cpf_cliente ?>'" >
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
                                        <form method="post" 
                                        action="#">
                                             <div class="containerBox">
                                                 <table id="formulario">
                                                    <tr>
                                                     <td>
                                                        <label for="cpf" class="titulo"><b>    CPF </b></label>
                                                        <input type="text" name="cpf_ins" id="cpf_add" oninput="cpf_replace(this.form.cpf_add.value, 'cpf_add')" placeholder="xxx.xxx.xxx-xx" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="CPF tem 11 dígitos (Digite apenas números)" maxlenght="15" required autofocus>
                                                        </td>
                                                    <td>
                                                        <label for="nome" class="titulo"><b>  Nome  </b></label>
                                                        <input type="text" placeholder="Nome" name="nome_ins" required>
                                                    </td>    
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td>
                                                            <label for="tel" class="titulo"> <b>  Telefone Residencial   </b></label>
                                                             <input type="tel" name="tel_res_ins"placeholder="(xx)xxxx-xxxx" maxlength="14" id="tel_add" oninput="tel_replace(this.form.tel_add.value, 'tel_add')" pattern="\([1-9]{2}\)[0-9]{4,5}-[0-9]{4}" title = "Telefone tem DDD + 8 ou 9 dígitos (Digite apenas números)">   
                                                         </td>
                                                         <td>
                                                              <label for="tel" class="titulo"> <b>  Telefone Celular </b></label>
                                                             <input type="tel" name="tel_cel_ins"placeholder="(xx)xxxx-xxxx" maxlength="14" id="cel_add" oninput="tel_replace(this.form.cel_add.value, 'cel_add')" pattern="\([1-9]{2}\)[0-9]{4,5}-[0-9]{4}" title = "Telefone tem DDD + 8 ou 9 dígitos (Digite apenas números)">                   
                                                         </td>
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td>
                                                            <label for="endereco" class="titulo"> <b>  Endereço  </b></label>
                                                            <input type="text" name="rua_ins" placeholder="Rua" required>
                                                         </td>
                                                         <td>
                                                            <label for="num" class="titulo"> <b>   Número </b></label>
                                                            <input type="text" name="numero_ins" placeholder="numero + complemento" required>
                                                         </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                        <label for="cep" class="titulo"> <b>   CEP    </b></label>
                                                        <input type="text" name = "cep_ins" placeholder="xxxxx-xxx" maxlength="9" id="cep_add" oninput="cep_replace(this.form.cep_add.value, 'cep_add')" pattern="\d{5}-\d{3}" title = "CEP tem 8 dígitos (Digite apenas números)" required>
                                                        </td>    
                                                     </tr>
                                                 </table>
                                              </div>
                                            <div class="botoes">
                                                <button type="reset" class="cancelbtn">Limpar</button>
                                                <button type="submit">Adicionar</button>
                                            </div>
                                            <input type="hidden" name="cadastro" value="cliente">
                                            
                                        </form>
                                    </div>
                                    <div id="alterar">
                                        <h3>Alterar Dados</h3>
                                        <form name = "alterar" method="post">
                                             <div class="containerBox">
                                                 <table id="formulario">
                                                   <tr>
                                                     <td>
                                                        <label for="cpf" class="titulo"><b>    CPF </b></label>
                                                        <input type="text" name="cpf_up" id="cpf_alt" oninput="cpf_replace(this.form.cpf_alt.value, 'cpf_alt')" placeholder="xxx.xxx.xxx-xx" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="CPF tem 11 dígitos (Digite apenas números)" maxlenght="15" required autofocus value="<?php echo $cpf_cliente; ?>">
                                                        </td>
                                                    <td>
                                                        <label for="nome" class="titulo"><b>  Nome  </b></label>
                                                        <input type="text" placeholder="Nome" name="nome_up" required value="<?php echo $nome_cliente; ?>">
                                                    </td>    
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td>
                                                            <label for="tel" class="titulo"> <b>  Telefone Residencial   </b></label>
                                                             <input type="tel" name="tel_res_up"placeholder="(xx)xxxx-xxxx" maxlength="14" id="tel_alt" oninput="tel_replace(this.form.tel_alt.value, 'tel_alt')" pattern="\([1-9]{2}\)[0-9]{4,5}-[0-9]{4}" title = "Telefone tem DDD + 8 ou 9 dígitos (Digite apenas números)" value="<?php echo $res_cliente; ?>">   
                                                         </td>
                                                         <td>
                                                              <label for="tel" class="titulo"> <b>  Telefone Celular </b></label>
                                                             <input type="tel" name="tel_cel_up"placeholder="(xx)xxxx-xxxx" maxlength="14" id="cel_alt" oninput="tel_replace(this.form.cel_alt.value, 'cel_alt')" pattern="\([1-9]{2}\)[0-9]{4,5}-[0-9]{4}" title = "Telefone tem DDD + 8 ou 9 dígitos (Digite apenas números)" value="<?php echo $cel_cliente; ?>">
                                                                                 
                                                         </td>
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td>
                                                            <label for="endereco" class="titulo"> <b>  Endereço  </b></label>
                                                            <input type="text" name="rua_up" placeholder="Rua" required value="<?php echo $rua_cliente; ?>">
                                                         </td>
                                                         <td>
                                                            <label for="num" class="titulo"> <b>   Número </b></label>
                                                            <input type="text" name="numero_up" placeholder="numero + complemento" required value="<?php echo $numero_cliente; ?>">
                                                         </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                        <label for="cep" class="titulo"> <b>   CEP    </b></label>
                                                        <input type="text" name = "cep_up" placeholder="xxxxx-xxx" maxlength="9" id="cep_alt" oninput="cep_replace(this.form.cep_alt.value, 'cep_alt')" pattern="\d{5}-\d{3}" title = "CEP tem 8 dígitos (Digite apenas números)" required value="<?php echo $cep_cliente; ?>">
                                                        </td>    
                                                     </tr>
                                                 </table>
                                              </div>
                                            <div class="botoes">
                                                <button type="button" class="voltar"><i class="material-icons" style="color:white; font-size: 30pt" onclick="window.history.back();">keyboard_return</i></button>
                                                <button name="confirmar" type="submit">Confirmar</button>
                                            </div>
                                             <input type="hidden" name="atualizar" value="cliente">
                                        </form>
                                 </div>
                                </td>
                            </tr>                                
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
        
        if(tamanho > 2) {
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
</script>
   