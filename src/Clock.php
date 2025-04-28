<?php

namespace GeoClock;

use GeoClock\Provider\ProviderInterface;
use GeoClock\Exception\ProviderException;
use Psr\Clock\ClockInterface;
use DateTimeImmutable;

class Clock implements ClockInterface
{
    private Config $config;
    private ?string $ip;

    /**
     * @param Config $config Provider configuration
     * @param string|null $ip IP address. If null, the external IP will be used.
     */
    public function __construct(Config $config, ?string $ip = null)
    {
        $this->config = $config;
        $this->ip = $ip;
    }

    /**
     * Returns the current DateTimeImmutable in the time zone specified by IP.
     *
     * @return DateTimeImmutable
     * @throws ProviderException If all providers failed to return the time.
     */
    public function now(): DateTimeImmutable
    {
        $exceptions = [];

        foreach ($this->config->getProviders() as $provider) {
            try {
                return $provider->getDateTime($this->ip);
            } catch (\Throwable $e) {
                $exceptions[] = $e;
            }
        }

        throw new ProviderException('All providers failed to fetch current time.', 0, end($exceptions) ?: null);
    }
}
