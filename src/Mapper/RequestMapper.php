<?php

namespace ArcheeNic\Cbr\Mapper;

use ArcheeNic\Cbr\Object\CurrencyItemObject;
use ArcheeNic\Cbr\Object\ExchangeItemObject;
use DateTime;
use RuntimeException;

class RequestMapper
{
    /**
     * @param  array<mixed>  $requestData
     * @param  DateTime      $date
     *
     * @return array<ExchangeItemObject>
     */
    public function dayItems(array $requestData, DateTime $date): array
    {
        $result = array_map(function (array $row) use ($date) {
            $value = $row['Value'] ?? null;
            if ($value) {
                $value = (float)str_replace(',', '.', $value);
            }

            $item = new ExchangeItemObject();
            $item->setCode($row['CharCode'] ?? null);
            $item->setValue($value);
            $item->setNominal($row['Nominal'] ?? null);
            $item->setDate($date);

            return $item;
        }, $requestData['Valute'] ?? []);

        $item = new ExchangeItemObject();
        $item->setCode('RUB');
        $item->setValue(1);
        $item->setNominal(1);
        $item->setDate($date);

        $result[] = $item;

        return $result;
    }

    /**
     * @param  CurrencyItemObject  $currency
     * @param  array<mixed>        $requestData
     *
     * @return array<ExchangeItemObject>
     */
    public function dynamicItems(CurrencyItemObject $currency, array $requestData): array
    {
        $data            = $requestData['Record'] ?? [];
        $previousSaleDay = null;

        $result = array_map(function (array $row) use ($currency, &$previousSaleDay) {
            $dateString = $row['@attributes']['Date'] ?? null;
            if (!$dateString) {
                throw new RuntimeException('Incorrect Date field');
            }
            $date = DateTime::createFromFormat('d.m.Y', $dateString);
            if (!$date) {
                throw new RuntimeException('Incorrect Date field');
            }

            if (!$previousSaleDay) {
                $previousSaleDay = clone $date;

                return null;
            }

            $value = $row['Value'] ?? null;
            if ($value) {
                $value = (float)str_replace(',', '.', $value);
            }

            $item = new ExchangeItemObject();
            $item->setCode($currency->getISOCharCode());
            $item->setValue($value);
            $item->setNominal($row['Nominal'] ?? null);
            $item->setDate($date);
            $item->setPreviousSaleDay($previousSaleDay);

            $previousSaleDay = clone $date;

            return $item;
        }, $data);

        return array_filter($result);
    }

    /**
     * @param  array<mixed>  $requestData
     *
     * @return array<CurrencyItemObject>
     */
    public function currencyItems(array $requestData): array
    {
        $data = $requestData['Item'] ?? [];
        if (!$data) {
            return $data;
        }

        $result = array_map(function (array $row) {
            $ISOCharCode = $row['ISO_Char_Code'] ?? null;
            if (is_array($ISOCharCode)) {
                $ISOCharCode = null;
            }
            $ISONumCode = $row['ISO_Num_Code'] ?? null;
            if (is_array($ISONumCode)) {
                $ISONumCode = null;
            }
            $item = new CurrencyItemObject();
            $item->setEngName($row['EngName'] ?? null);
            $item->setName($row['Name'] ?? null);
            $item->setNominal($row['Nominal'] ?? null);
            $item->setParentCode($row['ParentCode'] ?? null);
            $item->setISOCharCode($ISOCharCode);
            $item->setISONumCode($ISONumCode);

            return $item;
        }, $requestData['Item'] ?? []);

        $item = new CurrencyItemObject();
        $item->setISOCharCode('RUB');
        $item->setName('Российский рубль');
        $item->setEngName('Russian Rubble');
        $item->setNominal(1);
        $item->setParentCode('R00001');
        $result[] = $item;

        return $result;
    }
}
