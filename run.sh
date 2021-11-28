echo 'Iniciando:'
docker-compose up -d
docker exec -it php-transfer php artisan queue:work
