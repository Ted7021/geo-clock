<?php

namespace GeoClock\Provider;

use DateTimeImmutable;
use GeoClock\Exception\ProviderException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WorldTimeApiProvider implements ProviderInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://worldtimeapi.org/api/',
            'timeout' => 5.0,
        ]);
    }

    /**
     * Gets date and time via WorldTimeAPI.
     *
     * @param string|null $ip Server IP address. If null, the current server IP is used.
     * @return DateTimeImmutable
     * @throws ProviderException
     */
    public function getDateTime(?string $ip = null): DateTimeImmutable
    {
        try {
            $endpoint = $ip ? 'ip/' . $ip : 'ip';
            $response = $this->client->get($endpoint);
            $data = json_decode((string) $response->getBody(), true);

            if (empty($data['datetime'])) {
                throw new ProviderException('Invalid response from WorldTimeAPI.');
            }

            return new DateTimeImmutable($data['datetime']);
        } catch (GuzzleException|\Throwable $e) {
            throw new ProviderException('Failed to fetch time from WorldTimeAPI.', 0, $e);
        }
    }
}
