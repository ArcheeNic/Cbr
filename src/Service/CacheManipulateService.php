<?php

namespace ArcheeNic\Cbr\Service;

use ArcheeNic\Cbr\Config;
use ArcheeNic\Cbr\Object\CurrencyItemObject;
use ArcheeNic\Cbr\Object\ExchangeItemObject;
use DateTime;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AbstractTagAwareAdapter;

class CacheManipulateService
{
    private AbstractTagAwareAdapter $cache;
    private Config          $config;

    /**
     * @param  AbstractTagAwareAdapter  $cache
     * @param  Config           $config
     */
    public function __construct(AbstractTagAwareAdapter $cache, Config $config)
    {
        $this->cache  = $cache;
        $this->config = $config;
    }

    private function makeCurrencyKey(string $code, DateTime $date): string
    {
        return sprintf($this->config->getCacheKeyFormat(), $code, $date->format($this->config->getCacheDateFormat()));
    }

    private function makeReferenceKey(string $refCode): string
    {
        return sprintf($this->config->getCacheKeyFormat(), 'ref', $refCode);
    }

    /**
     * @return array<CurrencyItemObject>|null
     * @throws InvalidArgumentException
     */
    public function getCurrencyReference(): ?array
    {
        return $this->cache->get(
            $this->makeReferenceKey('currency'),
            function () { return null; }
        );
    }

    /**
     * @param  array<CurrencyItemObject>  $data
     *
     * @return void
     */
    public function setCurrencyReference(array $data): void
    {
        $key          = $this->makeReferenceKey('ref');
        $currencyItem = $this->cache->getItem($key)
            ->tag($this->config->getCacheTags())
            ->expiresAfter($this->config->getCacheRequestDaySeconds())
            ->set($data);
        $this->cache->save($currencyItem);
    }


    /**
     * @throws InvalidArgumentException
     */
    public function getDayResult(DateTime $date): ?bool
    {
        return $this->cache->get(
            $this->makeCurrencyKey('load', $date),
            function () { return null; }
        );
    }

    public function setDayResult(bool $result, DateTime $date): void
    {
        $key          = $this->makeCurrencyKey('load', $date);
        $currencyItem = $this->cache->getItem($key)
            ->tag($this->config->getCacheTags())
            ->expiresAfter($this->config->getCacheRequestDaySeconds())
            ->set($result);
        $this->cache->save($currencyItem);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getCurrency(string $code, DateTime $date): ?ExchangeItemObject
    {
        return $this->cache->get(
            $this->makeCurrencyKey($code, $date),
            function () { return null; }
        );
    }

    public function setCurrency(ExchangeItemObject $row): void
    {
        $key  = $this->makeCurrencyKey($row->getCode(), $row->getDate());
        $item = $this->cache->getItem($key)
            ->tag($this->config->getCacheTags())
            ->expiresAfter($this->config->getCacheDayCurrencySeconds())
            ->set($row);
        $this->cache->save($item);
    }
}
