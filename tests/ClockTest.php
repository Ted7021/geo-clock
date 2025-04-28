<?php

namespace GeoClock\Tests;

use DateTimeImmutable;
use GeoClock\Clock;
use GeoClock\Config;
use GeoClock\Exception\ProviderException;
use GeoClock\Provider\ProviderInterface;
use PHPUnit\Framework\TestCase;

class ClockTest extends TestCase
{
    public function testReturnsDateTimeFromFirstProvider(): void
    {
        // Create a mock provider that returns a fixed DateTimeImmutable
        $provider = $this->createMock(ProviderInterface::class);
        $provider->method('getDateTime')
            ->willReturn(new DateTimeImmutable('2025-04-28 12:00:00'));

        $config = new Config([$provider]);
        $clock = new Clock($config);

        $now = $clock->now();

        $this->assertInstanceOf(DateTimeImmutable::class, $now);
        $this->assertEquals('2025-04-28 12:00:00', $now->format('Y-m-d H:i:s'));
    }

    public function testFallsBackToSecondProvider(): void
    {
        // First provider throws an exception
        $provider1 = $this->createMock(ProviderInterface::class);
        $provider1->method('getDateTime')
            ->willThrowException(new \RuntimeException('Failed'));

        // Second provider returns a valid DateTimeImmutable
        $provider2 = $this->createMock(ProviderInterface::class);
        $provider2->method('getDateTime')
            ->willReturn(new DateTimeImmutable('2025-04-28 13:00:00'));

        $config = new Config([$provider1, $provider2]);
        $clock = new Clock($config);

        $now = $clock->now();

        $this->assertInstanceOf(DateTimeImmutable::class, $now);
        $this->assertEquals('2025-04-28 13:00:00', $now->format('Y-m-d H:i:s'));
    }

    public function testThrowsExceptionIfAllProvidersFail(): void
    {
        // All providers throw an exception
        $provider1 = $this->createMock(ProviderInterface::class);
        $provider1->method('getDateTime')
            ->willThrowException(new \RuntimeException('Failed 1'));

        $provider2 = $this->createMock(ProviderInterface::class);
        $provider2->method('getDateTime')
            ->willThrowException(new \RuntimeException('Failed 2'));

        $config = new Config([$provider1, $provider2]);
        $clock = new Clock($config);

        $this->expectException(ProviderException::class);

        $clock->now();
    }
}
