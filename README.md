# Predis Request Limiter

Request rate limiting for Predis.

## License

Licensed under MIT. Totally free for private or commercial projects.

## Installation

```text
composer require andrewdyer/predis-request-limiter
```

## Usage

```php
// Create new predis client instance
$predis = new Predis\Client([
    'scheme' => 'tcp',
    'host' => getenv('REDIS_HOST'),
    'port' => getenv('REDIS_PORT'),
    'password' => getenv('REDIS_PASSWORD')
]);

// Create new limiter instance
$limiter = new Anddye\PredisRequestLimiter\Limiter($predis);
$limiter->setRateLimit(10, 30)
    ->setStorageKey('api:limit:%s')
    ->setIdentifier(100);


if ($limiter->hasExceededRateLimit()) {
    // Too many requests has been made, display error message
} else {
    $limiter->incrementRequestCount();
}
```

## Support
   
If you are having any issues with this library, then please feel free to contact me on [Twitter](https://twitter.com/andyer92).

If you think you've found an bug, please report it using the [issue tracker](https://github.com/andrewdyer/predis-request-limiter/issues), or better yet, fork the repository and submit a pull request.

If you're using this package, I'd love to hear your thoughts!

## Useful Links

*   [Redis](http://redis.io/)
*   [Predis](https://github.com/nrk/predis)
*   [Predis Adaptor](https://github.com/andrewdyer/predis-adaptor)