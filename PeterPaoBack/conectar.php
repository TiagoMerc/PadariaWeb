<?php
    session_start();
    if ($_SESSION['vendedor'] == 1) {
        if(!@($conexao=pg_connect ("host=localhost dbname=Padaria port=5432 user=vendedor password=1234"))) {
            print "Não foi possível estabelecer uma conexão com o banco de dados.";
        }
        
    } else if ($_SESSION['gerente'] == 1) {
        if(!@($conexao=pg_connect ("host=localhost dbname=Padaria port=5432 user=gerente password=1234"))) {
            print "Não foi possível estabelecer uma conexão com o banco de dados.";
        }
        
    } else if ($_SESSION['padeiro'] == 1) {
        if(!@($conexao=pg_connect ("host=localhost dbname=Padaria port=5432 user=padeiro password=1234"))) {
            print "Não foi possível estabelecer uma conexão com o banco de dados.";
        }
        
    } else {
        if(!@($conexao=pg_connect ("host=localhost dbname=Padaria port=5432 user=admin password=1234"))) {
            print "Não foi possível estabelecer uma conexão com o banco de dados.";
        }
    }
        
    

?>