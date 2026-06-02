# Predis Request Limiter

A framework-agnostic PHP library for rate limiting requests using [Predis](https://github.com/predis/predis).

[![Latest Stable Version](http://poser.pugx.org/andrewdyer/predis-request-limiter/v?style=flat-square)](https://packagist.org/packages/andrewdyer/predis-request-limiter)
[![Total Downloads](http://poser.pugx.org/andrewdyer/predis-request-limiter/downloads?style=flat-square)](https://packagist.org/packages/andrewdyer/predis-request-limiter)
[![License](http://poser.pugx.org/andrewdyer/predis-request-limiter/license?style=flat-square)](https://packagist.org/packages/andrewdyer/predis-request-limiter)
[![PHP Version Require](http://poser.pugx.org/andrewdyer/predis-request-limiter/require/php?style=flat-square)](https://packagist.org/packages/andrewdyer/predis-request-limiter)

## Introduction

This library provides a request rate limiter for PHP applications, backed by Redis via Predis. The rate limit window, request threshold, storage key, and limit exceeded handler are all configurable, and any Predis-compatible client can be used as the backing store. The package is built on top of [andrewdyer/php-package-template](https://github.com/andrewdyer/php-package-template).

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
