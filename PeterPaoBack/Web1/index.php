<?php
    session_start();
    if(!isset($_SESSION['cpf'])){
        header("Location: login.php");
    }else{
        header("Location: home.php");
    }
?>