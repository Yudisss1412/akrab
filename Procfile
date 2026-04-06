# Procfile for Railway deployment
web: php artisan migrate --force && php artisan db:seed --force && frankenphp php-server -r public -l 0.0.0.0:$PORT
