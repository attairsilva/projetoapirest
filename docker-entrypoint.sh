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


/usr/local/bin/aguardar-banco.sh
# Inicia o servidor Apache **somente após todas as configurações**
echo "Iniciando Apache..."
exec apache2-foreground