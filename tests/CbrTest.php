<?php

namespace ArcheeNic\Cbr;

use DateTime;
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CbrTest extends TestCase
{
    private RedisTagAwareAdapter $cache;
    private Config               $config;
    private HttpClientInterface  $httpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $cacheClient      = RedisAdapter::createConnection('redis://redisCurrency:6379');
        $this->cache      = new RedisTagAwareAdapter($cacheClient);
        $this->config     = new Config();
        $this->httpClient = (new HttpClient)::create();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws JsonException
     * @throws TransportExceptionInterface
     */
    public function testGetExternallyCurrency(): void
    {
        $cbr = CbrFacade::init($this->config, $this->cache, $this->httpClient);

        $data = $cbr->getExternallyCurrency(DateTime::createFromFormat('Y-m-d', '2022-03-11'), 'USD', 'EUR');
        $this->assertEquals(
            $data,
            [
                "current" => 0.9054,
                "last"    => 0.9181,
                "diff"    => -0.0127,
            ]
        );
        $data = $cbr->getExternallyCurrency(DateTime::createFromFormat('Y-m-d', '2022-03-11'), 'USD', 'RUB');
        $this->assertEquals(
            [
                "current" => 120.3785,
                "last"    => 116.0847,
                "diff"    => 4.2938,
            ],
            $data
        );
        $data = $cbr->getExternallyCurrency(DateTime::createFromFormat('Y-m-d', '2022-03-11'), 'EUR', 'USD');
        $this->assertEquals(
            $data,
            [
                "current" => 1.1045,
                "last"    => 1.0892,
                "diff"    => 0.0153,
            ]
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws JsonException
     * @throws TransportExceptionInterface
     */
    public function testGetCurrency(): void
    {
        $cbr = CbrFacade::init($this->config, $this->cache, $this->httpClient);

        $data = $cbr->getCurrency(DateTime::createFromFormat('Y-m-d', '2022-03-11'), 'USD', 'EUR');
        $this->assertEquals($data, 0.9054);
        $data = $cbr->getCurrency(DateTime::createFromFormat('Y-m-d', '2022-03-11'), 'RUB', 'EUR');
        $this->assertEquals($data, 0.0075);
        $data = $cbr->getCurrency(DateTime::createFromFormat('Y-m-d', '2022-03-11'), 'EUR', 'USD');
        $this->assertEquals($data, 1.1045);
    }
}
