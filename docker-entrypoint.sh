#!/bin/bash
set -e

# Define o diretório do Laravel
APP_DIR="/var/www/html"

# Configura o tempo limite para aguardar iniciar o PostgreSQL
TIMEOUT=30
TIMER=0

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


# Acessa o diretório do Laravel
cd "$APP_DIR"
# Verifica se o .env já existe, se não, copia o env.exemplo
if [ ! -f "$APP_DIR/.env" ]; then
    if [ -f "$APP_DIR/env.exemplo" ]; then
        echo "Criando arquivo .env..."
        cp "$APP_DIR/env.exemplo" "$APP_DIR/.env"
    else
        echo "Erro: Arquivo env.exemplo não encontrado!"
        exit 1
    fi
fi

# Gera a chave do Laravel
echo "Gerando chave do Laravel..."
php artisan key:generate

# Executa as migrações
echo "Executando Migrates..."
php artisan migrate --seed --force

# Inicia o servidor Apache **somente após todas as configurações**
echo "Iniciando Apache..."
exec apache2-foreground