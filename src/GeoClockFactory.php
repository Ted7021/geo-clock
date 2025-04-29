<?php

namespace GeoClock;

use GeoClock\Provider\TimeApiProvider;
use GeoClock\Provider\WorldTimeApiProvider;
use GeoClock\IpResolver\IpifyResolver;
use Psr\Clock\ClockInterface;

class GeoClockFactory
{
    public static function createWithProviders(array $providers, ?string $ip = null): ClockInterface
    {
        $config = new Config($providers);
        return new Clock($config, $ip);
    }

    public static function create(?string $ip = null): ClockInterface
    {
        $ipResolver = new IpifyResolver();

        $providers = [
            new TimeApiProvider($ipResolver),
            new WorldTimeApiProvider(),
        ];

        return self::createWithProviders($providers, $ip);
    }
}
