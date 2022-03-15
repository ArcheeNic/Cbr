<?php

namespace ArcheeNic\Cbr;

class Config
{
    /**
     * @var string хост Центробанка
     */
    private string $apiHost = 'https://www.cbr.ru';

    /**
     * @var int сколько хранить статус получения курсов за день
     */
    private int $cacheRequestDaySeconds = 5;

    /**
     * @var int сколько хранить курс валюты за день
     */
    private int $cacheDayCurrencySeconds = 5;

    /**
     * @var int сколько хранить справочники
     */
    private int $cacheRefSeconds = 5;

    /**
     * @var string[] тэги кэша, например для инвалдации
     */
    private array $cacheTags = ['currency'];

    /**
     * Формат ключа
     * %1 - код валюты, например RUB
     * %2 - дата
     *
     * @var string
     */
    private string $cacheKeyFormat = 'currency_%1$s_%2$s';

    /**
     * @var string Формат даты для ключа кэша
     */
    private string $cacheDateFormat = 'Y_m_d';

    /**
     * @return int
     */
    public function getCacheRefSeconds(): int
    {
        return $this->cacheRefSeconds;
    }

    /**
     * @param  int  $cacheRefSeconds
     *
     * @return Config
     */
    public function setCacheRefSeconds(int $cacheRefSeconds): Config
    {
        $this->cacheRefSeconds = $cacheRefSeconds;

        return $this;
    }

    /**
     * @return string
     */
    public function getCacheKeyFormat(): string
    {
        return $this->cacheKeyFormat;
    }

    /**
     * @param  string  $cacheKeyFormat
     *
     * @return Config
     */
    public function setCacheKeyFormat(string $cacheKeyFormat): Config
    {
        $this->cacheKeyFormat = $cacheKeyFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getCacheDateFormat(): string
    {
        return $this->cacheDateFormat;
    }

    /**
     * @param  string  $cacheDateFormat
     *
     * @return Config
     */
    public function setCacheDateFormat(string $cacheDateFormat): Config
    {
        $this->cacheDateFormat = $cacheDateFormat;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getCacheTags(): array
    {
        return $this->cacheTags;
    }

    /**
     * @param  array|string[]  $cacheTags
     *
     * @return Config
     */
    public function setCacheTags(array $cacheTags): Config
    {
        $this->cacheTags = $cacheTags;

        return $this;
    }

    /**
     * @return int
     */
    public function getCacheRequestDaySeconds(): int
    {
        return $this->cacheRequestDaySeconds;
    }

    /**
     * @param  int  $cacheRequestDaySeconds
     *
     * @return Config
     */
    public function setCacheRequestDaySeconds(int $cacheRequestDaySeconds): Config
    {
        $this->cacheRequestDaySeconds = $cacheRequestDaySeconds;

        return $this;
    }

    /**
     * @return int
     */
    public function getCacheDayCurrencySeconds(): int
    {
        return $this->cacheDayCurrencySeconds;
    }

    /**
     * @param  int  $cacheDayCurrencySeconds
     *
     * @return Config
     */
    public function setCacheDayCurrencySeconds(int $cacheDayCurrencySeconds): Config
    {
        $this->cacheDayCurrencySeconds = $cacheDayCurrencySeconds;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiHost(): string
    {
        return $this->apiHost;
    }

    /**
     * @param  string  $apiHost
     *
     * @return Config
     */
    public function setApiHost(string $apiHost): Config
    {
        $this->apiHost = $apiHost;

        return $this;
    }
}
