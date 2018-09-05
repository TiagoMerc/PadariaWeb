#!/usr/bin/bash
#Variaveis
server="postgres01.dominio.com.br"   #Servidor postgres 
login="login_da_base"                #login da base
pw="*******"                         #senha
nome_temp="all"                      #nome do arquivo temporário postgres
bk="$HOME/backup_postgres/"          #Diretório para salvar arquivos de backup
nw=$(date "+%Y%m%d")                 #Buscar pela data
nb=3                                #número de cópias do banco de dados
function backup()
{
 echo "Realizando backup do servidor postgres"
 export PGPASSWORD=$pw
 pg_dump -v -F c -h $server $login -U $login -i > "$HOME/$hs.dmp"
 echo "Compactando arquivo de backup $fn.dmp.gz ..."
 gzip -f "$HOME/"$fn.dmp
 if [ -d $bk ]; then
   continue
 else
   mkdir $bk
 fi
 cp -f "$HOME/"$hs.dmp.gz "$bk/$nw.dmp.gz"
 a=0
 b=$(ls -t $bk)
 c=$nb
 for arq in $b; do
   a=$(($a+1))
   if [ "$a" -gt $c ];  then
     rm -f "$bk/$arq"
   fi
 done
}
backup postgres