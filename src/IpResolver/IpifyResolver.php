<?php

namespace GeoClock\IpResolver;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GeoClock\Exception\ProviderException;

class IpifyResolver implements IpResolverInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 5.0,
        ]);
    }

    /**
     * Resolves public IP using ipify.org service.
     *
     * @return string
     * @throws ProviderException
     */
    public function resolve(): string
    {
        try {
            $response = $this->client->get('https://api.ipify.org?format=json');
            $data = json_decode((string)$response->getBody(), true);

            if (empty($data['ip'])) {
                throw new ProviderException('Could not resolve public IP address.');
            }

            return $data['ip'];
        } catch (GuzzleException|\Throwable $e) {
            throw new ProviderException('Failed to resolve public IP address.', 0, $e);
        }
    }
}
