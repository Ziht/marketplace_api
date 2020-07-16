# marketplace_api

Подтягиваем зависимости composer install
В папке docker билдим docker-compose up
Запускаем php bin/rabbitmq.php(имитируем демона)
Выполняем создание Product через php bin/console.php app:create-products
Документация api через swagger http://localhost:8001/
Запускаем тесты ./vendor/bin/simple-phpunit
Далее например через Postman можно подтянуть конфиг swagger из config/swagger/swagger.yaml и попробовать создать счёт и провести оплату. 
Оплата выполняется в фоне для экономии времени пользователя, так что не пугайтесь того, что в ответе будет статус SEND.
