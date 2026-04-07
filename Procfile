# Procfile for Railway deployment
# Force redeploy to seed all data including products
web: php artisan migrate --force && php artisan db:seed --force && frankenphp php-server -r public -l 0.0.0.0:$PORT
