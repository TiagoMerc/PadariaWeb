<?php
if(!@($conexao=pg_connect ("host=localhost dbname=Padaria port=5432 user=postgres password=1234"))) {
   print "Não foi possível estabelecer uma conexão com o banco de dados.";
}
?>