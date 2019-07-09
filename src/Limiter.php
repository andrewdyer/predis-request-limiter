<?php

namespace Anddye\PredisRequestLimiter;

use Closure;
use Predis\Client as Predis;

/**
 * Class Limiter.
 *
 * @author Andrew Dyer <andrewdyer@outlook.com>
 */
class Limiter
{
    /**
     * @var int the unique identifier to use within the storage key
     */
    protected $identifier;

    /**
     * @var Closure the limit exceeded handler
     */
    protected $limitExceededHandler;

    /**
     * @var int the time limit that the defined requests can be made within
     */
    protected $perSecond = 60;

    /**
     * @var int requests that can be made as per the time limit
     */
    protected $requests = 30;

    /**
     * @var string the storage key used for the Redis store
     */
    protected $storageKey = 'rate:%s:requests';

    /**
     * Limiter constructor.
     *
     * @param Predis $redis
     */
    public function __construct(Predis $redis)
    {
        $this->redis = $redis;
        $this->identifier = $this->getIdentifier();
    }

    /**
     * Resolve the identifier used for checking requests.
     *
     * @param null $identifier
     *
     * @return mixed
     */
    public function getIdentifier($identifier = null)
    {
        if (null === $identifier) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return $identifier;
    }

    /**
     * Get the rate limit response.
     *
     * @return Closure
     */
    public function getLimitExceededHandler(): Closure
    {
        if (null === $this->limitExceededHandler) {
            return $this->defaultLimitExceededHandler();
        }

        return $this->limitExceededHandler;
    }

    /**
     * Get the identifier for the Redis storage key.
     *
     * @return string
     */
    public function getStorageKey(): string
    {
        return sprintf($this->storageKey, $this->identifier);
    }

    /**
     * Check if the rate limit has been exceeded.
     *
     * @return bool
     */
    public function hasExceededRateLimit(): bool
    {
        if ($this->redis->get($this->getStorageKey()) >= $this->requests) {
            return true;
        }

        return false;
    }

    /**
     * Increment the request count.
     */
    public function incrementRequestCount(): void
    {
        $this->redis->incr($this->getStorageKey());

        $this->redis->expire($this->getStorageKey(), $this->perSecond);
    }

    /**
     * Set the identifier used for checking requests.
     *
     * @param int $identifier
     *
     * @return Limiter
     */
    public function setIdentifier(int $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Set the handler for the limit being exceeded.
     *
     * @param callable $handler
     *
     * @return Limiter
     */
    public function setLimitExceededHandler(callable $handler): self
    {
        $this->limitExceededHandler = $handler;

        return $this;
    }

    /**
     * Set the limitations.
     *
     * @param int $requests
     * @param int $perSecond
     *
     * @return Limiter
     */
    public function setRateLimit(int $requests, int $perSecond): self
    {
        $this->requests = $requests;
        $this->perSecond = $perSecond;

        return $this;
    }

    /**
     * Set the storage key to be used for Redis.
     *
     * @param string $storageKey
     *
     * @return Limiter
     */
    public function setStorageKey(string $storageKey): self
    {
        $this->storageKey = $storageKey;

        return $this;
    }

    /**
     * The default limit exceeded handler.
     *
     * @return Closure
     */
    private function defaultLimitExceededHandler(): Closure
    {
        return function () {};
    }
}
