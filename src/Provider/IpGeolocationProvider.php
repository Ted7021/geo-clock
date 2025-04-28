<?php

namespace GeoClock\Provider;

use DateTimeImmutable;
use GeoClock\Exception\ProviderException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class IpGeolocationProvider implements ProviderInterface
{
    private Client $client;
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client([
            'base_uri' => 'https://api.ipgeolocation.io/',
            'timeout' => 5.0,
        ]);
    }

    /**
     * Fetches current DateTimeImmutable based on IP using ipgeolocation.io API.
     *
     * @param string|null $ip IP address. If null, external IP will be used.
     * @return DateTimeImmutable
     * @throws ProviderException
     */
    public function getDateTime(?string $ip = null): DateTimeImmutable
    {
        try {
            $endpoint = 'timezone?apiKey=' . urlencode($this->apiKey);
            if ($ip) {
                $endpoint .= '&ip=' . urlencode($ip);
            }

            $response = $this->client->get($endpoint);
            $data = json_decode((string) $response->getBody(), true);

            if (empty($data['date_time']) || empty($data['timezone']) ) {
                throw new ProviderException('Invalid response from ipgeolocation.io API.');
            }

            return new DateTimeImmutable($data['date_time'], new \DateTimeZone($data['timezone']));
        } catch (GuzzleException|\Throwable $e) {
            throw new ProviderException('Failed to fetch time from ipgeolocation.io API.', 0, $e);
        }
    }
}
