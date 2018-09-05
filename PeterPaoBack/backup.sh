
#!/usr/bin/bash
#Variaveis
server="localhost"   #Servidor postgres 
login="postgres"                #login da base
pw="1234"                         #senha
nome_temp="all"                      #nome do arquivo temporário postgres
bk="/home/sy/Downloads/LabBD/backup_postgres/"          #Diretório para salvar arquivos de backup
nw=$(date "+%Y%m%d")                 #Buscar pela data
nb=3                                #número de cópias do banco de dados
function backup()
{
 echo "Realizando backup do servidor postgres"
 export PGPASSWORD=$pw
 pg_dump -v -F c -h $server -U $login -d Padaria > "$HOME/$hs.dmp"
 echo "Compactando arquivo de backup $fn.dmp.gz ..."
 gzip -f "$HOME/"$fn.dmp
 if ! [[ -d $bk ]]; then
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
