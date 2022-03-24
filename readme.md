# сборка тестовой среды
```bash
docker-compose up -d
docker exec -it currency-php-1 composer install
```
# Тестирование

```bash
docker exec -it currency-php-1 vendor/bin/phpunit
```

# PHP Stan

```bash
docker exec -it currency-php-1 vendor/bin/phpstan
```

# Use

```php
    $cacheClient      = RedisAdapter::createConnection('redis://redisCurrency:6379');
    $cache            = new RedisTagAwareAdapter($cacheClient);
    $config           = new Config();
    $httpClient       = (new HttpClient)::create();

    $cbr = CbrFacade::init($config, $cache, $httpClient);
```

# Получить курс валюты на выбранный день без учёта прошлого торгового дня

```php
        $day = \DateTime::createFromFormat('Y-m-d', '2022-03-11')
        $data = $cbr->getCurrency($day, 'USD', 'EUR');

```

# Получить курс валюты на выбранный день с учётом прошлого торгового дня

```php
        $day = \DateTime::createFromFormat('Y-m-d', '2022-03-11')
        $data = $cbr->getExternallyCurrency($day, 'USD', 'EUR');
```
