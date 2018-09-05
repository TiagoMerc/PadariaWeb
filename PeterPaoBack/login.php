<?php
    //parte separada com verificação de erros
    /* SOBRE A VARIAVEL ERRO DE LOGIN
            0 - Nenhum erro
            1 - Usuário não encontrado
            2 - Tentativa de acesso sem estar logado
            //apenas usadas pelo metodo get nas paginas com login efetuado
            3 - Funcionário não é gerente
    */
    if(isset($_GET['erro_login'])){
        if($_GET['erro_login'] == 2){
            $message = "Você precisa ter efetuado login para acessar a página!";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
    }
?>

<?php
    //inicia uma sessão
    session_start();
    
    //se foi requisitado limpar com o cookie salvo
    if(isset($_GET['reset'])){
        setcookie('peterPao_user_login', '', 1);
        setcookie('peterPao_userpass_login', '', 1);
        header("Location: login.php");
    }

    //tratamento de sessao e login
    if((isset($_SESSION['log']))){
        header("Location: home.php");
    }else{        
        if(isset($_POST['cpf']))
        {

            include('conectar.php');

            $cpf = ($_POST['cpf']); //recebe cpf do form
            $senha = ($_POST['senha']); //recebe cpf do form

            //trata cpf
            $cpf = str_replace(".", "", $cpf);
            $cpf = str_replace("-", "", $cpf);

            //executa pesquisa
            $querry = "SELECT cpf_funcio, nome_funcio, cargo
                       FROM funcionario
                       WHERE cpf_funcio = '$cpf' AND senha = md5('$senha')
                       LIMIT 1";

            //verifica se houve retorno
            if($resultado = pg_query($conexao, $querry)){
                //verifica numero de retornos
                if(pg_num_rows($resultado) > 0){
                    //tranforma a tupla em objeto
                    $tupla = pg_fetch_object($resultado);

                    //inicia uma sessão
                    session_start();

                    //dá valores a campos da sessãp
                    $_SESSION['log'] = 1;
                    $_SESSION['cpf'] = $tupla->cpf_funcio;
                    $_SESSION['nome'] = $tupla->nome_funcio;
                    $_SESSION['cargo'] = $tupla->cargo;
                    $_SESSION['hora_login'] = date("H") . "h  " . date("i") . "m";
                    $_SESSION['cod_pedido'] = -1;

                    $_SESSION['gerente'] = 0;
                    $_SESSION['vendedor'] = 0;
                    $_SESSION['padeiro'] = 0;
                    
                    if(strcmp($_SESSION['cargo'], "Gerente") == 0){
                        $_SESSION['gerente'] = 1;
                    }else if (strcmp($_SESSION['cargo'], "Vendedor") == 0) {
                        $_SESSION['vendedor'] = 1;
                    } else {
                        $_SESSION['padeiro'] = 1;
                    }
                    
                    //tira o reset se existir
                    if(isset($_SESSION['reset'])){
                        unset($_SESSION['reset']);
                    }

                    //fecha objeto resultado
                    pg_lo_close($resultado);
                    
                    //seta o coockie com login e senha para 30 dias ou dá unset
                    if(isset($_POST[remember])){
                        setcookie('peterPao_user_login', $_POST['cpf'], time()+60*60*24*30);
                        setcookie('peterPao_userpass_login', $senha, time()+60*60*24*30);
                    }else{
                        setcookie('peterPao_user_login', '', 1);
                        setcookie('peterPao_userpass_login', '', 1);
                    }
                    
                    //remove qualquer encomenda não terminada ou resto de cookie
                    if(isset($_COOKIE['value_p'])){  
                        $del = $_COOKIE['value_p'];

                        setcookie('value_p', '', 1);
                        $delete = "DELETE FROM pedido WHERE cod_pedido = '$del'";

                    }

                    //leva para home
                    header("Location: home.php");
                }else{
                    //erro_login = 1
                    $message = "Erro! Verifique se o CPF e a senha foram digitadas corretamente";
                    echo "<script type='text/javascript'>alert('$message');</script>";
                }
            }else{
                die("Erro!!! ". pg_last_error($conexao));
            }

            pg_close($conexao);
        }
    }
?>



<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Peter Pão Login</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/login.css">
    </head>
    <body>
        
       <h1> Peter Pão </h1>
        <div class="imgcontainer">
            <img src="Img/paoIcon.png" alt="Avatar" class="avatar">
        </div>
        <form  onsubmit="check(this)" method="POST">
          <div class="containerBox">
            <label><b>CPF</b></label>
            <input for="cpf" type="text" oninput="cpf_replace(this.form.cpf.value)" placeholder="xxx.xxx.xxx-xx" name="cpf" id="cpf" pattern="\d{3}.\d{3}.\d{3}-\d{2}" title="CPF possui 11 números (Digite apenas os números)" maxlength="15" value="<?php if(isset($_COOKIE['peterPao_user_login'])){echo $_COOKIE['peterPao_user_login'];} ?>" required autofocus>

            <label><b>Senha</b></label>
            <input for="senha" id="senha" type="password" placeholder="Senha" name="senha" value="<?php if(isset($_COOKIE['peterPao_userpass_login'])){echo $_COOKIE['peterPao_userpass_login'];} ?>" required>
              
              <button type="submit" value="Login">Login</button>
            <label>
              <input type="checkbox" checked="checked" name="remember"> Lembrar usuário
            </label>
          </div>
          <div class="containerBox" >
            <button type="reset" class="cancelbtn" <?php 
                if(isset($_COOKIE['peterPao_userpass_login'])){
                    echo "onclick=\"reload_clear()\"";
                }
            ?>>Limpar</button>
            <span class="psw">Esqueceu a senha? Fale com um gerente!</span>
          </div>
        </form>
    </body>
</html>

<script language="javascript" type="text/javascript">
    function reload_clear(){
        window.location.replace(window.location.href + "?reset=1");
    }
    
    function cpf_replace(pCpf){
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
            var cpf = tokens.join(".");
        }

        //substitui no input
        document.getElementById("cpf").value = cpf
    }
</script>