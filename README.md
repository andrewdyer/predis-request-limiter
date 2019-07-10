# Predis Request Limiter

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/a360f55ae33c445587e9a66eb4ccb115)](https://www.codacy.com/app/andrewdyer/predis-request-limiter?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=andrewdyer/predis-request-limiter&amp;utm_campaign=Badge_Grade)
[![Latest Stable Version](https://poser.pugx.org/andrewdyer/predis-request-limiter/v/stable)](https://packagist.org/packages/andrewdyer/predis-request-limiter)
[![Total Downloads](https://poser.pugx.org/andrewdyer/predis-request-limiter/downloads)](https://packagist.org/packages/andrewdyer/predis-request-limiter)
[![Daily Downloads](https://poser.pugx.org/andrewdyer/predis-request-limiter/d/daily)](https://packagist.org/packages/andrewdyer/predis-request-limiter)
[![Monthly Downloads](https://poser.pugx.org/andrewdyer/predis-request-limiter/d/monthly)](https://packagist.org/packages/andrewdyer/predis-request-limiter)
[![Latest Unstable Version](https://poser.pugx.org/andrewdyer/predis-request-limiter/v/unstable)](https://packagist.org/packages/andrewdyer/predis-request-limiter)
[![License](https://poser.pugx.org/andrewdyer/predis-request-limiter/license)](https://packagist.org/packages/andrewdyer/predis-request-limiter)

Request rate limiting using Predis.

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
