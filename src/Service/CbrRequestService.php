<?php

namespace ArcheeNic\Cbr\Service;

use ArcheeNic\Cbr\Config;
use DateTime;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CbrRequestService
{
    private HttpClientInterface $client;
    private Config              $config;

    public function __construct(HttpClientInterface $client, Config $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param  DateTime  $date
     *
     * @return mixed[]|null
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function daily(DateTime $date): ?array
    {
        $request = $this->client->request(
            'GET',
            $this->config->getApiHost() . '/scripts/XML_daily.asp?date_req=' . $date->format('d/m/Y')
        );

        $content = $request->getContent();
        if (!$content) {
            return null;
        }

        $data = json_decode(
            json_encode(simplexml_load_string($request->getContent()), JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return $data;
    }

    /**
     * @param  string    $parentCode
     * @param  DateTime  $start
     * @param  DateTime  $finish
     *
     * @return mixed[]|null
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function dynamic(string $parentCode, DateTime $start, DateTime $finish): ?array
    {
        // http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=02/03/2001&date_req2=14/03/2001&VAL_NM_RQ=R01235
        $request = $this->client->request(
            'GET',
            $this->config->getApiHost() . '/scripts/XML_dynamic.asp?date_req1=' . $start->format('d/m/Y')
            . '&date_req2=' . $finish->format('d/m/Y') . '&VAL_NM_RQ=' . $parentCode
        );

        $content = $request->getContent();
        if (!$content) {
            return null;
        }

        $data = json_decode(
            json_encode(simplexml_load_string($request->getContent()), JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return $data;
    }

    /**
     * @return mixed[]|null
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function currencyReference(): ?array
    {
        // http://www.cbr.ru/scripts/XML_val.asp?d=0
        $request = $this->client->request(
            'GET',
            $this->config->getApiHost() . '/scripts/XML_valFull.asp'
        );

        $content = $request->getContent();
        if (!$content) {
            return null;
        }

        $data = json_decode(
            json_encode(simplexml_load_string($request->getContent()), JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return $data;
    }
}
