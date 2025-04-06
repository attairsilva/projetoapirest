#!/bin/sh

echo "⏳ Esperando o banco iniciar em $DB_HOST:$DB_PORT..."

until nc -z "$DB_HOST" "$DB_PORT" > /dev/null 2>&1; do
  echo "⏳ Aguardando o banco em $DB_HOST:$DB_PORT..."
  sleep 2
done

echo "✅ Banco disponível! Verificando se já foi migrado..."

if ! php artisan migrate:status | grep -q 'Migration table not found'; then
  echo "✅ Migrations já aplicadas. Pulando migrate --seed."
else
  echo "🚀 Rodando migrate --seed pela primeira vez..."
  php artisan migrate --seed
fi