#!/bin/bash
set -e

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

# # Copia o .env se ele não existir
# if [ ! -f ".env" ]; then
#     if [ -f ".env.renomeie" ]; then
#         echo "Criando arquivo .env..."
#         cp ".env.renomeie" ".env"
#     else
#         echo "Erro: Arquivo env.exemplo não encontrado!"
#         # echo "Diretório atual: $(pwd)"
#         # ls -lah  # Lista os arquivos no diretório atual
#         exit 1
#     fi
# fi

# # Gera a chave do Laravel
# echo "Gerando chave do Laravel..."
# php artisan key:generate

# # Executa as migrações
# # echo "Executando Migrates..."
# # composer dump-autoload
# # php artisan config:clear
# php artisan migrate:refresh --seed 

# Inicia o servidor Apache **somente após todas as configurações**
echo "Iniciando Apache..."
exec apache2-foreground