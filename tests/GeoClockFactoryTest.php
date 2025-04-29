<?php

namespace GeoClock\Tests;

use GeoClock\GeoClockFactory;
use Psr\Clock\ClockInterface;
use PHPUnit\Framework\TestCase;

class GeoClockFactoryTest extends TestCase
{
    public function testCanCreateClockWithDefaultProviders(): void
    {
        $clock = GeoClockFactory::create();
        $this->assertInstanceOf(ClockInterface::class, $clock);
    }

    public function testCanCreateClockWithIpGeolocationProvider(): void
    {
        $clock = GeoClockFactory::create(null, 'fake-api-key');
        $this->assertInstanceOf(ClockInterface::class, $clock);
    }
}
