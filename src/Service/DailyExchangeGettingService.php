<?php

namespace ArcheeNic\Cbr\Service;

use ArcheeNic\Cbr\Mapper\RequestMapper;
use ArcheeNic\Cbr\Object\ExchangeItemObject;
use DateTime;
use JsonException;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class DailyExchangeGettingService
{

    private CacheManipulateService $cacheManipulateService;
    private CbrRequestService      $cbrRequestService;
    private RequestMapper          $requestMapper;

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
     * @param  DateTime  $date
     * @param  string[]  $codes
     *
     * @return ExchangeItemObject[]
     * @throws CacheException
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getByDate(DateTime $date, array $codes): array
    {
        $dayResult = $this->cacheManipulateService->getDayResult($date);
        if ($dayResult === null) {
            $dayResult = $this->getData($date);
        }

        if (!$dayResult) {
            throw new RuntimeException("Exchange not load from CBR on " . $date->format('Y-m-d'));
        }

        $result = [];
        foreach ($codes as $value) {
            $result[$value] = $this->cacheManipulateService->getCurrency($value, $date);
            if (!$result[$value]) {
                throw new RuntimeException(sprintf('Exchange "%s" on "%s" not found', $value, $date->format('Y-m-d')));
            }
        }

        return array_filter($result);
    }

    /**
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws CacheException
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    private function getData(DateTime $date): bool
    {
        $result = false;

        $requestData = $this->cbrRequestService->daily($date);

        if ($requestData) {
            $itemsArray = $this->requestMapper->dayItems($requestData, $date);
        } else {
            $itemsArray = [];
        }

        if ($itemsArray) {
            foreach ($itemsArray as $item) {
                $this->cacheManipulateService->setCurrency($item);
            }
            $result = true;
        }

        $this->cacheManipulateService->setDayResult($result, $date);

        return $result;
    }
}
