# Procfile for Railway deployment
# Production config - no auto-seed to prevent dummy data accumulation
web: php artisan migrate --force && frankenphp php-server -r public -l 0.0.0.0:$PORT
