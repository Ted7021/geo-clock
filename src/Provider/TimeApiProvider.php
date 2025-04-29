<?php

namespace GeoClock\Provider;

use DateTimeImmutable;
use GeoClock\Exception\ProviderException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TimeApiProvider implements ProviderInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://timeapi.io/api/',
            'timeout' => 5.0,
        ]);
    }

    /**
     * Fetches current DateTimeImmutable based on IP using timeapi.io API.
     *
     * @param string|null $ip IP address. If null, external IP will be used.
     * @return DateTimeImmutable
     * @throws ProviderException
     */
    public function getDateTime(?string $ip = null): DateTimeImmutable
    {
        try {
            $endpoint = $ip ? 'Time/current/ip?ipAddress=' . urlencode($ip) : 'Time/current/ip';

            $response = $this->client->get($endpoint);
            $data = json_decode((string) $response->getBody(), true);

            if (empty($data['dateTime']) || empty($data['timeZone'])) {
                throw new ProviderException('Invalid response from timeapi.io API.');
            }

            return new DateTimeImmutable($data['dateTime'], new \DateTimeZone($data['timeZone']));
        } catch (GuzzleException|\Throwable $e) {
            throw new ProviderException('Failed to fetch time from timeapi.io API.', 0, $e);
        }
    }
}
