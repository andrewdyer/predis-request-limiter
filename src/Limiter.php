<?php

declare(strict_types=1);

namespace AndrewDyer\PredisRequestLimiter;

use Predis\ClientInterface;

/**
 * Handles request rate limiting using a Redis-backed counter.
 */
class Limiter
{
    /**
     * The configured limit exceeded handler.
     *
     * @var callable|null
     */
    private $limitExceededHandler = null;

    /**
     * The time window in seconds within which the request limit applies.
     */
    private int $perSecond = 60;

    /**
     * The maximum number of requests allowed within the time window.
     */
    private int $requests = 30;

    /**
     * The storage key template used for the Redis store.
     */
    private string $storageKey = 'rate:%s:requests';

    /**
     * Creates a new Limiter with the required dependencies.
     *
     * @param ClientInterface $client The Predis client instance.
     * @param string $identifier The unique identifier for the storage key.
     */
    public function __construct(
        private readonly ClientInterface $client,
        private readonly string $identifier,
    ) {
        $this->limitExceededHandler = static function(): void {
        };
    }

    /**
     * Returns the Predis client instance.
     *
     * @return ClientInterface The Predis client.
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Returns the unique identifier used within the storage key.
     *
     * @return string The identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the configured limit exceeded handler, or the default if none has been set.
     *
     * @return callable The limit exceeded handler.
     */
    public function getLimitExceededHandler(): callable
    {
        return $this->limitExceededHandler;
    }

    /**
     * Returns the time window in seconds within which the request limit applies.
     *
     * @return int The time window in seconds.
     */
    public function getPerSecond(): int
    {
        return $this->perSecond;
    }

    /**
     * Returns the maximum number of requests allowed within the time window.
     *
     * @return int The request limit.
     */
    public function getRequests(): int
    {
        return $this->requests;
    }

    /**
     * Returns the formatted Redis storage key for this limiter instance.
     *
     * @return string The storage key.
     */
    public function getStorageKey(): string
    {
        return sprintf($this->storageKey, $this->identifier);
    }

    /**
     * Determines whether the rate limit has been exceeded.
     *
     * @return bool True if the limit has been exceeded, false otherwise.
     */
    public function hasExceededRateLimit(): bool
    {
        return $this->client->get($this->getStorageKey()) >= $this->requests;
    }

    /**
     * Handles incrementing the request count and refreshing the TTL window.
     */
    public function incrementRequestCount(): void
    {
        $this->client->incr($this->getStorageKey());
        $this->client->expire($this->getStorageKey(), $this->perSecond);
    }

    /**
     * Registers a custom limit exceeded handler.
     *
     * @param callable $limitExceededHandler The handler to invoke when the limit is exceeded.
     * @return self The current instance for method chaining.
     */
    public function setLimitExceededHandler(callable $limitExceededHandler): self
    {
        $this->limitExceededHandler = $limitExceededHandler;

        return $this;
    }

    /**
     * Registers the rate limit configuration.
     *
     * @param int $requests The maximum number of requests allowed within the time window.
     * @param int $perSecond The time window in seconds.
     * @return self The current instance for method chaining.
     */
    public function setRateLimit(int $requests, int $perSecond): self
    {
        $this->requests = $requests;
        $this->perSecond = $perSecond;

        return $this;
    }

    /**
     * Registers the storage key template.
     *
     * @param string $storageKey The key template, which must contain a %s placeholder for the identifier.
     * @return self The current instance for method chaining.
     */
    public function setStorageKey(string $storageKey): self
    {
        $this->storageKey = $storageKey;

        return $this;
    }
}
