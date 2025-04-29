<?php

namespace GeoClock\Tests\Provider;

use DateTimeImmutable;
use GeoClock\Exception\ProviderException;
use GeoClock\IpResolver\IpResolverInterface;
use GeoClock\Provider\TimeApiProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class TimeApiProviderTest extends TestCase
{
    public function testReturnsDateTimeSuccessfully(): void
    {
        $expectedIp = '8.8.8.8';
        $expectedDateTime = '2025-04-28T16:00:00';
        $expectedTimeZone = 'Europe/Kiev';

        $ipResolver = $this->createMock(IpResolverInterface::class);
        $ipResolver->method('resolve')
            ->willReturn($expectedIp);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('https://timeapi.io/api/Time/current/ip?ipAddress=' . urlencode($expectedIp))
            ->willReturn(new Response(200, [], json_encode([
                'dateTime' => $expectedDateTime,
                'timeZone' => $expectedTimeZone,
            ])));

        $provider = new TimeApiProvider($ipResolver);
        $this->injectClient($provider, $client);

        $dateTime = $provider->getDateTime();

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);
        $this->assertSame($expectedDateTime, $dateTime->format('Y-m-d\TH:i:s'));
        $this->assertSame($expectedTimeZone, $dateTime->getTimezone()->getName());
    }

    public function testThrowsExceptionWhenIpResolverFails(): void
    {
        $this->expectException(ProviderException::class);

        $ipResolver = $this->createMock(IpResolverInterface::class);
        $ipResolver->method('resolve')
            ->willThrowException(new ProviderException('Failed to resolve IP'));

        $provider = new TimeApiProvider($ipResolver);

        $provider->getDateTime();
    }

    public function testThrowsExceptionOnInvalidResponse(): void
    {
        $this->expectException(ProviderException::class);

        $ipResolver = $this->createMock(IpResolverInterface::class);
        $ipResolver->method('resolve')
            ->willReturn('8.8.8.8');

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->willReturn(new Response(200, [], '{}'));

        $provider = new TimeApiProvider($ipResolver);
        $this->injectClient($provider, $client);

        $provider->getDateTime();
    }

    private function injectClient(TimeApiProvider $provider, Client $client): void
    {
        $reflection = new \ReflectionClass($provider);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($provider, $client);
    }
}
