# сборка тестовой среды

docker-compose up -d docker exec -it currency-php-1 composer install

# Тестирование

docker exec -it currency-php-1 vendor/bin/phpunit

# PHP Stan

docker exec -it currency-php-1 vendor/bin/phpstan

# Use

```php
    $cacheClient      = RedisAdapter::createConnection('redis://redisCurrency:6379');
    $this->cache      = new RedisTagAwareAdapter($cacheClient);
    $this->config     = new Config();
    $this->httpClient = (new HttpClient)::create();

    $cbr = CbrFacade::init($this->config, $this->cache, $this->httpClient);
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
