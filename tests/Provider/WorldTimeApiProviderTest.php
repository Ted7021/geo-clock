<?php

namespace GeoClock\Tests\Provider;

use DateTimeImmutable;
use GeoClock\Exception\ProviderException;
use GeoClock\Provider\WorldTimeApiProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class WorldTimeApiProviderTest extends TestCase
{
    public function testReturnsDateTimeSuccessfully(): void
    {
        $expectedDateTime = '2025-04-28T15:30:00.000Z';

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('ip')
            ->willReturn(new Response(200, [], json_encode(['datetime' => $expectedDateTime])));

        $provider = new WorldTimeApiProvider();
        $this->injectClient($provider, $client);

        $dateTime = $provider->getDateTime();

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertSame((new DateTimeImmutable($expectedDateTime))->format('c'), $dateTime->format('c'));
    }

    public function testThrowsExceptionOnInvalidResponse(): void
    {
        $this->expectException(ProviderException::class);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('ip')
            ->willReturn(new Response(200, [], '{}'));

        $provider = new WorldTimeApiProvider();
        $this->injectClient($provider, $client);

        $provider->getDateTime();
    }

    private function injectClient(WorldTimeApiProvider $provider, Client $client): void
    {
        $reflection = new \ReflectionClass($provider);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($provider, $client);
    }
}
