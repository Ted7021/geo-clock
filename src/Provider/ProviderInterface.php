<?php

namespace GeoClock\Provider;

use DateTimeImmutable;

interface ProviderInterface
{
    /**
     * Gets the date and time based on the server's IP address.
     *
     * @param string|null $ip IP address of the server. If not passed, the external IP is used.
     * @return DateTimeImmutable
     */
    public function getDateTime(?string $ip = null): DateTimeImmutable;
}
