<!--
    INSTRUÇÕES:
        -- Para usar apenas a verificação JS, basta descomentar a função check na linha 87
           e retirar todo o procedimento de PHP (no começo e no meio .
-->
<?php
    //controle de login do ur=suario
    $erro_login = 0; 

    if(isset($_POST['cpf']))
    {
  
        include('conectar.php');
    
        $cpf = ($_POST['cpf']);
        $senha = ($_POST['senha']);
        
        $select = "SELECT *
                   FROM userdatalogin
                   WHERE cpf = '".$cpf."' AND senha = MD5('".$senha."')
                   LIMIT 1";
        
        if($resultado = $conexao->query($select)){
            if($resultado->num_rows > 0){
                $tupla = $resultado->fetch_object();
                session_start();
                echo $tupla->nome;
                $_SESSION['log'] = 1;
                $_SESSION['cpf'] = $tupla->cpf;
                $_SESSION['nome'] = $tupla->nome;
                
                $resultado->close();
                
                header("Location: home.html");
            }else{
                $erro_login = 1; 
            }
        }else{
            die("Erro!!! ". $mysql->error);
        }
        
        $conexao->close();
    }
?>



<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Peter Pão Login</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/login.css">
    </head>
    <body>
        
       <h1> Peter Pão </h1>
        <div class="imgcontainer">
            <img src="../Img/paoIcon.png" alt="Avatar" class="avatar">
        </div>
        <form  onsubmit="check(this)" method="POST">
          <div class="containerBox">
            <label><b>CPF</b></label>
            <input for="cpf" type="text" oninput="cpf_replace(this.form.cpf.value)" placeholder="xxx.xxx.xxx-xx" name="cpf" id="cpf" pattern="\d{3}.\d{3}.\d{3}-\d{2}" title="CPF possui 11 números (Digite apenas os números)" maxlength="15" required autofocus>

            <label><b>Senha</b></label>
            <input for="senha" id="senha" type="password" placeholder="Senha" name="senha" required>
              
              <button type="submit" value="Login">Login</button>
            <label>
              <input type="checkbox" checked="checked" name="remember"> Remember me
            </label>
          </div>
          <div class="containerBox" >
            <button type="button" class="cancelbtn">Limpar</button>
            <span class="psw">Forgot <a href="#">password?</a></span>
          </div>
        </form>
    </body>
</html>

<script language="javascript" type="text/javascript">
    /*function check(form){
        if(form.cpf.value == "000.000.000-00" && form.senha.value == "1234"){
            window.open("home.html");
            this.window.close();
        } else {
            alert("Erro! Verifique se o CPF e a senha foram digitadas corretamente")
        }
    }*/ 

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

<?php
if($erro_login == 1)
    {
        $message = "Erro! Verifique se o CPF e a senha foram digitadas corretamente";
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
?>