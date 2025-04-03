#!/bin/bash
set -e

# Configura o tempo limite para aguardar iniciar o PostgreSQL
TIMEOUT=30
TIMER=0

echo "Aguardando o banco PostgreSQL iniciar..."
while ! pg_isready -h db -p 5432 -U user; do
  sleep 2
  TIMER=$((TIMER+2))

  if [ $TIMER -ge $TIMEOUT ]; then
    echo "O banco PostgreSQL não esta respondendo."
    exit 1
  fi
done

echo "Banco de dados pronto!"

# Gera a chave do Laravel
echo "Gerando chave do Laravel..."
php artisan key:generate

# Executa as migrações
echo "Executando Migrates..."
php artisan migrate --force

# Inicia o servidor Apache
echo "Iniciando Apache..."
exec apache2-foreground


