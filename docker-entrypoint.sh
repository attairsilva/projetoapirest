#!/bin/bash
set -e

# Configura o tempo limite para aguardar iniciar o PostgreSQL
TIMEOUT=30
TIMER=0

# echo "Aguardando o banco PostgreSQL iniciar..."
# while ! pg_isready -h postgres_db -p 5432 -U user; do
#   sleep 2
#   TIMER=$((TIMER + 2))

#   if [ $TIMER -ge $TIMEOUT ]; then
#     echo "O banco PostgreSQL não está respondendo após $TIMEOUT segundos."
#     exit 1
#   fi
# done

echo "Aguardando o MinIO iniciar..."
while ! curl -s http://minio:9000/minio/health/live >/dev/null; do
  sleep 2
  TIMER=$((TIMER + 2))
  if [ $TIMER -ge $TIMEOUT ]; then
    echo "O MinIO não está respondendo após $TIMEOUT segundos."
    exit 1
  fi
done
echo "MinIO está pronto!"

echo "Banco de dados pronto!"

# Gera a chave do Laravel
echo "Gerando chave do Laravel..."
php artisan key:generate

# Executa as migrações
echo "Executando Migrates..."
php artisan migrate --seed --force

# Inicia o servidor Apache **somente após todas as configurações**
echo "Iniciando Apache..."
exec apache2-foreground