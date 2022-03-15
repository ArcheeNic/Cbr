<?php

namespace ArcheeNic\Cbr\Service;

use ArcheeNic\Cbr\Mapper\RequestMapper;
use ArcheeNic\Cbr\Object\CurrencyItemObject;
use ArcheeNic\Cbr\Object\ExchangeItemObject;
use DateInterval;
use DateTime;
use JsonException;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class DynamicExchangeGettingService
{

    private CacheManipulateService $cacheManipulateService;
    private CbrRequestService      $cbrRequestService;
    private RequestMapper          $requestMapper;
    private DefaultCreatingService $defaultCreatingService;

    public function __construct(
        CacheManipulateService $cacheManipulateService,
        CbrRequestService $cbrRequestService,
        RequestMapper $requestMapper,
        DefaultCreatingService $defaultCreatingService
    ) {
        $this->cacheManipulateService = $cacheManipulateService;
        $this->cbrRequestService      = $cbrRequestService;
        $this->requestMapper          = $requestMapper;
        $this->defaultCreatingService = $defaultCreatingService;
    }


    /**
     * @param  CurrencyItemObject  $currency
     * @param  DateTime            $date
     *
     * @return ExchangeItemObject|null
     * @throws CacheException
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getByDate(CurrencyItemObject $currency, DateTime $date): ?ExchangeItemObject
    {
        if ($currency->getISOCharCode() === 'RUB') {
            return $this->defaultCreatingService->RUB($date);
        }

        $currencyExchangeItem = $this->cacheManipulateService->getCurrency($currency->getISOCharCode(), $date);
        if (!$currencyExchangeItem) {
            $this->getData($currency, (clone $date)->sub(new DateInterval('P10D')), $date);
            $currencyExchangeItem = $this->cacheManipulateService->getCurrency($currency->getISOCharCode(), $date);
        }

        if (!$currencyExchangeItem) {
            throw new RuntimeException(
                sprintf('Exchange "%s" on "%s" not found', $currency->getISOCharCode(), $date->format('Y-m-d'))
            );
        }

        return $currencyExchangeItem;
    }

    /**
     * @param  CurrencyItemObject  $currency
     * @param  DateTime            $start
     * @param  DateTime            $end
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws CacheException
     */
    private function getData(CurrencyItemObject $currency, DateTime $start, DateTime $end): void
    {
        $requestData = $this->cbrRequestService->dynamic($currency->getParentCode(), $start, $end);

        if ($requestData) {
            $itemsArray = $this->requestMapper->dynamicItems($currency, $requestData);
        } else {
            $itemsArray = [];
        }

        if ($itemsArray) {
            foreach ($itemsArray as $item) {
                $this->cacheManipulateService->setCurrency($item);
            }
        }
    }
}
