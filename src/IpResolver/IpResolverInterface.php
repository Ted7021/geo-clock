<?php

namespace GeoClock\IpResolver;

interface IpResolverInterface
{
    /**
     * Resolves and returns the current public IP address.
     *
     * @return string
     */
    public function resolve(): string;
}
