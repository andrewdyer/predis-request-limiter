<h1 align="center">Predis Request Limiter</h1>

<p align="center">Request rate limiting using Predis.</p>

<p align="center">
    <a href="https://packagist.org/packages/andrewdyer/predis-request-limiter"><img src="https://poser.pugx.org/andrewdyer/predis-request-limiter/downloads?style=for-the-badge" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/andrewdyer/predis-request-limiter"><img src="https://poser.pugx.org/andrewdyer/predis-request-limiter/v?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/andrewdyer/predis-request-limiter"><img src="https://poser.pugx.org/andrewdyer/predis-request-limiter/license?style=for-the-badge" alt="License"></a>
</p>

## License

Licensed under MIT. Totally free for private or commercial projects.

## Installation

```text
composer require andrewdyer/predis-request-limiter
```

## Usage

```php
// Create new predis client instance
$predis = new Predis\Client();

// Create new limiter instance
$limiter = new Anddye\PredisRequestLimiter\Limiter($predis, 100);
$limiter->setRateLimit(10, 30)
    ->setStorageKey('api:limit:%s');

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
*   [Predis Adaptor](https://github.com/andrewdyer/predis-request-limiter)
