echo 'Iniciando:'
docker-compose up -d
echo 'Dump autload'
docker exec -it php-transfer composer dump-autoload
docker exec -it php-transfer composer install
echo 'Copia do .env'
cd transfer && cp .env.example .env
echo 'Executar Migrations'
docker exec -it php-transfer php artisan migrate
docker exec -it php-transfer php artisan db:seed
echo 'Iniciando fila de notificações'
docker exec -it php-transfer php artisan quere:work
