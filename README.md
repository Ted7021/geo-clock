# GeoClock

**GeoClock** is a lightweight PHP library that implements [PSR-20 ClockInterface](https://www.php-fig.org/psr/psr-20/) and returns the current time based on your server’s external IP address.

It supports multiple external providers with automatic fallback if the first one fails.

---

## Installation

```bash
composer require ted7021/geo-clock
```

---

## Features

- Returns `DateTimeImmutable` based on actual timezone from IP.
- Uses external providers like [WorldTimeAPI](https://worldtimeapi.org), [TimeAPI.io](https://timeapi.io), [ipgeolocation.io](https://ipgeolocation.io).
- Easily extendable: implement your own provider.
- PSR-20 compatible (`ClockInterface`).

---

## Usage

```php
use GeoClock\GeoClockFactory;

$clock = GeoClockFactory::create();

echo $clock->now()->format('Y-m-d H:i:s');
```

Want to specify IP?
```php
$clock = GeoClockFactory::create('8.8.8.8');
```

---

## Advanced usage with custom providers

```php
use GeoClock\GeoClockFactory;
use GeoClock\Provider\WorldTimeApiProvider;

$clock = GeoClockFactory::createWithProviders([
    new WorldTimeApiProvider(),
]);

echo $clock->now()->format('Y-m-d H:i:s');
```

---

## Providers

GeoClock includes the following providers:

| Provider | Requires API key? |
|----------|-------------------|
| `WorldTimeApiProvider` | ❌ No |
| `TimeApiProvider` | ❌ No |
| `IpGeolocationProvider` | ✅ Yes |

---

## Configuration: Timeout and Retries

Each provider supports configurable timeout and retries:

- **Timeout**: maximum number of seconds to wait for a response (default `5.0` seconds).
- **Retries**: number of retry attempts on network errors (default `3` retries).

### Example: Custom timeout and retries

```php
use GeoClock\Provider\WorldTimeApiProvider;

// Create a provider with 10 seconds timeout and 5 retries
$provider = new WorldTimeApiProvider(timeout: 10.0, maxRetries: 5);
```

---

## Testing

```bash
vendor/bin/phpunit
```

## Requirements

- PHP 8.2+
- ext-curl
- Composer
