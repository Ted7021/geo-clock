<?php

namespace GeoClock\Http;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Exception\GuzzleException;

class HandlerStackFactory
{
    /**
     * Creates a Guzzle HandlerStack with retry middleware.
     *
     * @param int $maxRetries
     * @return HandlerStack
     */
    public static function createWithRetry(int $maxRetries = 3): HandlerStack
    {
        $handlerStack = HandlerStack::create();

        $handlerStack->push(Middleware::retry(
            function ($retries, $request, $response, $exception) use ($maxRetries) {
                if ($retries >= $maxRetries) {
                    return false;
                }

                return $exception instanceof GuzzleException;
            },
            function ($retries) {
                return 1000 * $retries; // exponential backoff: 1s, 2s, 3s
            }
        ));

        return $handlerStack;
    }
}
