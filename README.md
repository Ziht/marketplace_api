# marketplace_api

Подтягиваем зависимости composer install <br>
В папке docker билдим docker-compose up <br>
Запускаем php bin/rabbitmq.php(имитируем демона), или используем docker/images/php/rabbitmq для запуска как linux сервис<br>
Выполняем создание Product через php bin/console.php app:create-products <br>
Документация api через swagger http://localhost:8001/ <br>
Запускаем тесты ./vendor/bin/simple-phpunit <br>
Далее например через Postman можно подтянуть конфиг swagger из config/swagger/swagger.yaml и попробовать создать счёт и провести оплату.  <br>
Оплата выполняется в фоне для экономии времени пользователя, так что не пугайтесь того, что в ответе будет статус SEND. <br>
