# Procfile for Railway deployment
# Force redeploy attempt 2 - trigger build
web: php artisan migrate --force && php artisan db:seed --force && frankenphp php-server -r public -l 0.0.0.0:$PORT
