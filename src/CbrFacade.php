<?php

namespace ArcheeNic\Cbr;

use ArcheeNic\Cbr\Mapper\RequestMapper;
use ArcheeNic\Cbr\Service\CacheManipulateService;
use ArcheeNic\Cbr\Service\CbrRequestService;
use ArcheeNic\Cbr\Service\CurrencyReferenceGettingService;
use ArcheeNic\Cbr\Service\DailyExchangeGettingService;
use ArcheeNic\Cbr\Service\DefaultCreatingService;
use ArcheeNic\Cbr\Service\DynamicExchangeGettingService;
use Symfony\Component\Cache\Adapter\AbstractTagAwareAdapter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CbrFacade
{
    public static function init(
        Config $config,
        AbstractTagAwareAdapter $cache,
        HttpClientInterface $httpClient
    ): Cbr {
        $cacheManipulateService = new CacheManipulateService($cache, $config);
        $cdrRequestService      = new CbrRequestService($httpClient, $config);
        $requestMapper          = new RequestMapper();
        $defaultCreatingService = new DefaultCreatingService();

        return new Cbr(
            new DailyExchangeGettingService    ($cacheManipulateService, $cdrRequestService, $requestMapper),
            new CurrencyReferenceGettingService($cacheManipulateService, $cdrRequestService, $requestMapper),
            new DynamicExchangeGettingService  (
                $cacheManipulateService,
                $cdrRequestService,
                $requestMapper,
                $defaultCreatingService
            )
        );
    }
}
