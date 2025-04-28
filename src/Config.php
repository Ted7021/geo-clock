<?php

namespace GeoClock;

use GeoClock\Provider\ProviderInterface;

class Config
{
    /**
     * @var ProviderInterface[]
     */
    private array $providers;

    /**
     * @param ProviderInterface[] $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * Get a list of time providers.
     *
     * @return ProviderInterface[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }
}
