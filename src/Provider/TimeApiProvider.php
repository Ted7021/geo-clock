<?php

namespace GeoClock\Provider;

use DateTimeImmutable;
use GeoClock\Exception\ProviderException;
use GeoClock\Http\HandlerStackFactory;
use GeoClock\IpResolver\IpResolverInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TimeApiProvider implements ProviderInterface
{
    private Client $client;
    private IpResolverInterface $ipResolver;

    public function __construct(IpResolverInterface $ipResolver, float $timeout = 5.0, int $maxRetries = 3)
    {
        $this->ipResolver = $ipResolver;

        $this->client = new Client([
            'base_uri' => 'https://timeapi.io/api/',
            'timeout' => $timeout,
            'handler' => HandlerStackFactory::createWithRetry($maxRetries),
        ]);
    }

    /**
     * Fetches current DateTimeImmutable using timeapi.io based on IP.
     *
     * @param string|null $ip IP address. If null, it will resolve automatically.
     * @return DateTimeImmutable
     * @throws ProviderException
     */
    public function getDateTime(?string $ip = null): DateTimeImmutable
    {
        try {
            if ($ip === null) {
                $ip = $this->ipResolver->resolve();
            }

            $response = $this->client->get('https://timeapi.io/api/Time/current/ip?ipAddress=' . urlencode($ip));
            $data = json_decode((string)$response->getBody(), true);

            if (empty($data['dateTime']) || empty($data['timeZone'])) {
                throw new ProviderException('Invalid response from timeapi.io API.');
            }

            return new DateTimeImmutable($data['dateTime'], new \DateTimeZone($data['timeZone']));
        } catch (GuzzleException|\Throwable $e) {
            throw new ProviderException('Failed to fetch time from timeapi.io API.', 0, $e);
        }
    }
}
