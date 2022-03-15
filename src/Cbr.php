<?php

namespace ArcheeNic\Cbr;

use ArcheeNic\Cbr\Object\ExchangeItemObject;
use ArcheeNic\Cbr\Service\CurrencyReferenceGettingService;
use ArcheeNic\Cbr\Service\DailyExchangeGettingService;
use ArcheeNic\Cbr\Service\DynamicExchangeGettingService;
use DateTime;
use JsonException;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Cbr
{
    private DailyExchangeGettingService     $dailyExchangeGettingService;
    private CurrencyReferenceGettingService $currencyReferenceGettingService;
    private DynamicExchangeGettingService   $dynamicExchangeGettingService;

    public function __construct(
        DailyExchangeGettingService $dailyExchangeGettingService,
        CurrencyReferenceGettingService $currencyReferenceGettingService,
        DynamicExchangeGettingService $dynamicExchangeGettingService,
    ) {
        $this->dailyExchangeGettingService     = $dailyExchangeGettingService;
        $this->currencyReferenceGettingService = $currencyReferenceGettingService;
        $this->dynamicExchangeGettingService   = $dynamicExchangeGettingService;
    }


    /**
     * Получить курс валюты на конкретную дату
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getCurrency(DateTime $date, string $from, string $to): float
    {
        $currencyArray  = [$from, $to];
        $currentDayData = $this->dailyExchangeGettingService->getByDate($date, $currencyArray);

        return $this->mathConversion($currentDayData[$from], $currentDayData[$to]);
    }

    private function mathConversion(ExchangeItemObject $from, ExchangeItemObject $to): float
    {
        return round($from->getValue() / $from->getNominal() / $to->getValue() * $to->getNominal(), 4);
    }

    /**
     * Получить курс валюты и разницу с прошлым торговым днём
     * @return array<float>
     * @throws ServerExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws TransportExceptionInterface
     */
    public function getExternallyCurrency(DateTime $date, string $from, string $to): array
    {
        $fromObject = $this->currencyReferenceGettingService->getByISOCharCode($from);
        if (!$fromObject) {
            throw new RuntimeException('Not found currency by iso name' . $from);
        }

        $toObject = $this->currencyReferenceGettingService->getByISOCharCode($to);
        if (!$toObject) {
            throw new RuntimeException('Not found currency by iso name ' . $to);
        }

        $currentDayData = [
            $from => $this->dynamicExchangeGettingService->getByDate($fromObject, $date),
            $to   => $this->dynamicExchangeGettingService->getByDate($toObject, $date),
        ];
        $prevDataKey    = $from;
        if ($from === 'RUB') {
            $prevDataKey = $to;
        }

        $lastDayData = [
            $from => $this->dynamicExchangeGettingService->getByDate(
                $fromObject,
                $currentDayData[$prevDataKey]->getPreviousSaleDay()
            ),
            $to   => $this->dynamicExchangeGettingService->getByDate(
                $toObject,
                $currentDayData[$prevDataKey]->getPreviousSaleDay()
            ),
        ];

        $current = $this->mathConversion($currentDayData[$from], $currentDayData[$to]);
        $last    = $this->mathConversion($lastDayData[$from], $lastDayData[$to]);

        return [
            'current' => $current,
            'last'    => $last,
            'diff'    => round($current - $last, 4),
        ];
    }
}
