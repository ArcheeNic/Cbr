<?php

namespace ArcheeNic\Cbr\Service;

use ArcheeNic\Cbr\Object\ExchangeItemObject;
use DateInterval;
use DateTime;

class DefaultCreatingService
{
    public function RUB(DateTime $date): ExchangeItemObject
    {
        $item = new ExchangeItemObject();
        $item->setCode('RUB');
        $item->setValue(1);
        $item->setNominal(1);
        $item->setDate($date);
        $item->setPreviousSaleDay((clone $date)->sub(new DateInterval('P1D')));

        return $item;
    }
}
