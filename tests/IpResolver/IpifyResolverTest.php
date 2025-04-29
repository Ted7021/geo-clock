<?php

namespace GeoClock\Tests\IpResolver;

use GeoClock\Exception\ProviderException;
use GeoClock\IpResolver\IpifyResolver;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class IpifyResolverTest extends TestCase
{
    public function testResolvesIpSuccessfully(): void
    {
        $expectedIp = '8.8.8.8';

        // Mock Guzzle client
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('https://api.ipify.org?format=json')
            ->willReturn(new Response(200, [], json_encode(['ip' => $expectedIp])));

        // Inject mocked client via reflection (dirty but fast for private prop)
        $resolver = new IpifyResolver();
        $reflection = new \ReflectionClass($resolver);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($resolver, $client);

        $ip = $resolver->resolve();

        $this->assertSame($expectedIp, $ip);
    }

    public function testThrowsExceptionOnInvalidResponse(): void
    {
        $this->expectException(ProviderException::class);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('https://api.ipify.org?format=json')
            ->willReturn(new Response(200, [], '{}'));

        $resolver = new IpifyResolver();
        $reflection = new \ReflectionClass($resolver);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($resolver, $client);

        $resolver->resolve();
    }
}
