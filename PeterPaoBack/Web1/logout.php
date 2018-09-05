<?php
    session_start();

    //tira os sets da sessao
    unset($_SESSION['log']);
    unset($_SESSION['cpf']);
    unset($_SESSION['cargo']);
    unset($_SESSION['gerente']);

    //tira o cookie do remember-me
    if(isset($_COOKIE['peterPao_userpass_login'])){
        setcookie('peterPao_user_login', '', 1);
        setcookie('peterPao_userpass_login', '', 1);
    }

    //acab com sessão
    session_destroy();
    
    //joga de volta para o index
    header("Location: index.php");
?>