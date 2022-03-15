<?php

namespace ArcheeNic\Cbr\Service;

use ArcheeNic\Cbr\Mapper\RequestMapper;
use ArcheeNic\Cbr\Object\CurrencyItemObject;
use JsonException;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CurrencyReferenceGettingService
{
    private CacheManipulateService $cacheManipulateService;
    private CbrRequestService      $cbrRequestService;
    private RequestMapper          $requestMapper;
    /**
     * @var CurrencyItemObject[]
     */
    private array $currencyReference = [];


    public function __construct(
        CacheManipulateService $cacheManipulateService,
        CbrRequestService $cbrRequestService,
        RequestMapper $requestMapper
    ) {
        $this->cacheManipulateService = $cacheManipulateService;
        $this->cbrRequestService      = $cbrRequestService;
        $this->requestMapper          = $requestMapper;
    }

    /**
     * @return array<CurrencyItemObject>
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws CacheException
     */
    public function get(): array
    {
        if ($this->currencyReference) {
            return $this->currencyReference;
        }

        $currencyReference = $this->cacheManipulateService->getCurrencyReference();
        if ($currencyReference === null) {
            $currencyReference = $this->getData();
        }

        if (!$currencyReference) {
            throw new RuntimeException("Currency reference not load from CBR");
        }

        $this->currencyReference = $currencyReference;

        return $this->currencyReference;
    }

    /**
     * @param  string  $code
     *
     * @return CurrencyItemObject|null
     * @throws CacheException
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getByISOCharCode(string $code): ?CurrencyItemObject
    {
        foreach ($this->get() as $currency) {
            if ($currency->getISOCharCode() === $code) {
                return $currency;
            }
        }

        return null;
    }

    /**
     * @return array<CurrencyItemObject>
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws CacheException
     */
    private function getData(): array
    {
        $requestData = $this->cbrRequestService->currencyReference();

        if ($requestData) {
            $itemsArray = $this->requestMapper->currencyItems($requestData);
        } else {
            $itemsArray = [];
        }

        if ($itemsArray) {
            $this->cacheManipulateService->setCurrencyReference($itemsArray);
        }

        return $itemsArray;
    }
}
