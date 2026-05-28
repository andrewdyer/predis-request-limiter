![Predis Request Limiter](https://public-assets.andrewdyer.rocks/images/covers/predis-request-limiter.png)

<p align="center">
  <a href="https://packagist.org/packages/andrewdyer/predis-request-limiter"><img src="https://poser.pugx.org/andrewdyer/predis-request-limiter/v/stable?style=for-the-badge" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/andrewdyer/predis-request-limiter"><img src="https://poser.pugx.org/andrewdyer/predis-request-limiter/downloads?style=for-the-badge" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/andrewdyer/predis-request-limiter"><img src="https://poser.pugx.org/andrewdyer/predis-request-limiter/license?style=for-the-badge" alt="License"></a>
  <a href="https://packagist.org/packages/andrewdyer/predis-request-limiter"><img src="https://poser.pugx.org/andrewdyer/predis-request-limiter/require/php?style=for-the-badge" alt="PHP Version Required"></a>
</p>

<p align="center">
  Built on top of <a href="https://github.com/andrewdyer/php-package-template">andrewdyer/php-package-template</a>
</p>

# Predis Request Limiter

A framework-agnostic PHP library for rate limiting requests using Redis.

## Introduction

This library provides a request rate limiter for PHP applications, tracking request counts against configurable limits using Redis as the backing store via [Predis](https://github.com/predis/predis). The storage key, rate limit, and limit exceeded handler are all configurable.

## Prerequisites

- **[PHP](https://www.php.net/)**: Version 8.3 or higher is required.
- **[Composer](https://getcomposer.org/)**: Dependency management tool for PHP.
- **[Redis](https://redis.io/)**: A running Redis instance is required.

## Installation

```bash
composer require andrewdyer/predis-request-limiter
```

## Getting Started

### 1. Create a Predis client

```php
use Predis\Client;

$client = new Client([
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 6379,
]);
```

### 2. Create a limiter

Instantiate `Limiter` with a Predis client and a unique identifier. The identifier is interpolated into the storage key to namespace requests per user, IP, or endpoint:

```php
use AndrewDyer\PredisRequestLimiter\Limiter;

$limiter = new Limiter($client, '127.0.0.1');
```

### 3. Configure the rate limit

Set the maximum number of requests and the time window in seconds:

```php
$limiter->setRateLimit(requests: 10, perSecond: 60);
```

## Usage

### Checking and incrementing

Check whether the limit has been exceeded before incrementing the request count:

```php
if ($limiter->hasExceededRateLimit()) {
    // Limit exceeded — respond with 429 or invoke the handler
} else {
    $limiter->incrementRequestCount();
}
```

### Setting a limit exceeded handler

Register a callable to invoke when the limit is exceeded:

```php
$limiter->setLimitExceededHandler(function (): void {
    http_response_code(429);
    echo 'Too many requests.';
    exit;
});
```

Then invoke it when the limit is exceeded:

```php
if ($limiter->hasExceededRateLimit()) {
    ($limiter->getLimitExceededHandler())();
} else {
    $limiter->incrementRequestCount();
}
```

### Customising the storage key

The default storage key template is `rate:%s:requests`, where `%s` is replaced by the identifier. Override it to namespace keys differently:

```php
$limiter->setStorageKey('api:limit:%s');
```

With the identifier `127.0.0.1`, the resolved key becomes `api:limit:127.0.0.1`.

## License

Licensed under the [MIT licence](https://opensource.org/licenses/MIT) and is free for private or commercial projects.
