#!/bin/bash
set -e


echo "Iniciando Apache..."
exec apache2-foreground

# Espera o serviço minio estar pronto 
# export MC_CONFIG_DIR=/var/www/html/.mc
# while ! mc alias set myminio http://127.0.0.1:9001 admin adminpassword ; do
#   echo "Aguardando MinIO..."
#   sleep 5
# done
# exec "$@"

# echo "MinIO está pronto!"


