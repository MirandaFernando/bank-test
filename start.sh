#!/bin/bash

# Espera o MySQL ficar pronto
while ! mysqladmin ping -h"mysql" -P"3306" -u"root" -p"root" --silent; do
    echo "Aguardando MySQL ficar pronto..."
    sleep 1
done

# Executa migrações e seeders (se necessário)
php artisan migrate --force

# Inicia o servidor PHP em segundo plano
php artisan serve --host=0.0.0.0 --port=8000 &

# Inicia o Vite para desenvolvimento
if [ "$APP_ENV" = "local" ]; then
    npm run dev -- --host 0.0.0.0 --port 5173
else
    # Em produção, apenas mantém o container rodando
    tail -f /dev/null
fi
