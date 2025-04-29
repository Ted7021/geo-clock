<?php

namespace GeoClock\Tests\Provider;

use DateTimeImmutable;
use GeoClock\Exception\ProviderException;
use GeoClock\Provider\IpGeolocationProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class IpGeolocationProviderTest extends TestCase
{
    public function testReturnsDateTimeSuccessfully(): void
    {
        $datetime = '2025-04-28 15:45:00';
        $timezone = 'Europe/Kiev';

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->willReturn(new Response(200, [], json_encode([
                'date_time' => $datetime,
                'timezone' => $timezone,
            ])));

        $provider = new IpGeolocationProvider('fake-api-key');
        $this->injectClient($provider, $client);

        $dateTime = $provider->getDateTime();

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertSame("$datetime", $dateTime->format('Y-m-d H:i:s'));
        $this->assertSame($timezone, $dateTime->getTimezone()->getName());
    }

    public function testThrowsExceptionOnInvalidResponse(): void
    {
        $this->expectException(ProviderException::class);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->willReturn(new Response(200, [], '{}'));

        $provider = new IpGeolocationProvider('fake-api-key');
        $this->injectClient($provider, $client);

        $provider->getDateTime();
    }

    private function injectClient(IpGeolocationProvider $provider, Client $client): void
    {
        $reflection = new \ReflectionClass($provider);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($provider, $client);
    }
}
