# Procfile for Railway deployment
web: php artisan migrate --force && php artisan db:seed --class=RoleSeeder --force && php artisan db:seed --class=UserSeeder --force && frankenphp php-server -r public -l 0.0.0.0:$PORT
