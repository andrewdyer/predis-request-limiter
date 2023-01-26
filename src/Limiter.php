<?php

namespace Anddye\PredisRequestLimiter;

use Predis\Client;

class Limiter
{
    /**
     * Client class used for connecting and executing commands on Redis.
     */
    private Client $client;

    /**
     * The unique identifier to use within the storage key.
     */
    private string $identifier;

    /**
     * The limit exceeded handler.
     */
    private $limitExceededHandler;

    /**
     * The time limit that the defined requests can be made within.
     */
    private int $perSecond = 60;

    /**
     * Requests that can be made as per the time limit.
     */
    private int $requests = 30;

    /**
     * The storage key used for the Redis store.
     */
    private string $storageKey = 'rate:%s:requests';

    /**
     * @param Client $client     client class used for connecting and executing commands on Redis
     * @param string $identifier unique identifier to use within the storage key
     */
    public function __construct(Client $client, string $identifier)
    {
        $this->client = $client;
        $this->identifier = $identifier;
    }

    /**
     * The default limit exceeded handler.
     */
    public function defaultLimitExceededHandler(): callable
    {
        return function () {};
    }

    /**
     * Get Client class used for connecting and executing commands on Redis.
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Get unique identifier to use within the storage key.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get the limit exceeded handler.
     */
    public function getLimitExceededHandler(): callable
    {
        if (!$this->limitExceededHandler) {
            return $this->defaultLimitExceededHandler();
        }

        return $this->limitExceededHandler;
    }

    /**
     * Get the time limit that the defined requests can be made within.
     */
    public function getPerSecond(): int
    {
        return $this->perSecond;
    }

    /**
     * Get requests that can be made as per the time limit.
     */
    public function getRequests(): int
    {
        return $this->requests;
    }

    /**
     * Get storage key.
     */
    public function getStorageKey(): string
    {
        return sprintf($this->storageKey, $this->getIdentifier());
    }

    /**
     * Check if the rate limit has been exceeded.
     */
    public function hasExceededRateLimit(): bool
    {
        if ($this->getClient()->get($this->getStorageKey()) >= $this->getRequests()) {
            return true;
        }

        return false;
    }

    /**
     * Increment the request count.
     */
    public function incrementRequestCount(): void
    {
        $this->getClient()->incr($this->getStorageKey());

        $this->getClient()->expire($this->getStorageKey(), $this->getPerSecond());
    }

    /**
     * Set limit exceeded handler.
     *
     * @param callable $limitExceededHandler the limit exceeded handler
     */
    public function setLimitExceededHandler(callable $limitExceededHandler): self
    {
        $this->limitExceededHandler = $limitExceededHandler;

        return $this;
    }

    /**
     * Set rate limit.
     *
     * @param int $requests  requests that can be made as per the time limit
     * @param int $perSecond the time limit that the defined requests can be made within
     */
    public function setRateLimit(int $requests, int $perSecond): self
    {
        $this->requests = $requests;
        $this->perSecond = $perSecond;

        return $this;
    }

    /**
     * set storage key.
     *
     * @param string $storageKey the storage key used for the Redis store
     */
    public function setStorageKey(string $storageKey): self
    {
        $this->storageKey = $storageKey;

        return $this;
    }
}
