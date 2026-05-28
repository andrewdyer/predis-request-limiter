<?php

declare(strict_types=1);

namespace AndrewDyer\PredisRequestLimiter\Tests;

use AndrewDyer\PredisRequestLimiter\Limiter;
use AndrewDyer\PredisRequestLimiter\Tests\Support\FakeClient;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Limiter.
 */
final class LimiterTest extends TestCase
{
    /**
     * The fake Predis client used across tests.
     */
    private FakeClient $client;

    /**
     * Sets up the test environment before each test.
     */
    protected function setUp(): void
    {
        $this->client = new FakeClient();
        $this->client->flushall();
    }

    /**
     * Asserts that getLimitExceededHandler returns a callable when no custom handler has been set.
     */
    public function testGetLimitExceededHandlerReturnsDefaultWhenNoneSet(): void
    {
        $limiter = new Limiter($this->client, 'test-default-handler');

        $this->assertIsCallable($limiter->getLimitExceededHandler());
    }

    /**
     * Asserts that hasExceededRateLimit returns false while the request count is below the configured limit.
     */
    public function testHasExceededRateLimitReturnsFalseBeforeLimitReached(): void
    {
        $limiter = new Limiter($this->client, 'test-has-exceeded-rate-limit');
        $limiter->setRateLimit(3, 30);

        $limiter->incrementRequestCount();
        $this->assertFalse($limiter->hasExceededRateLimit());

        $limiter->incrementRequestCount();
        $this->assertFalse($limiter->hasExceededRateLimit());
    }

    /**
     * Asserts that hasExceededRateLimit returns true once the request count reaches the configured limit.
     */
    public function testHasExceededRateLimitReturnsTrueWhenLimitReached(): void
    {
        $limiter = new Limiter($this->client, 'test-has-exceeded-rate-limit');
        $limiter->setRateLimit(3, 30);

        $limiter->incrementRequestCount();
        $limiter->incrementRequestCount();
        $limiter->incrementRequestCount();

        $this->assertTrue($limiter->hasExceededRateLimit());
    }

    /**
     * Asserts that incrementing the request count correctly updates the stored value.
     */
    public function testIncrementRequestCountUpdatesStoredValue(): void
    {
        $limiter = new Limiter($this->client, 'test-increment-request-count');

        $limiter->incrementRequestCount();
        $this->assertSame('1', $limiter->getClient()->get($limiter->getStorageKey()));

        $limiter->incrementRequestCount();
        $this->assertSame('2', $limiter->getClient()->get($limiter->getStorageKey()));

        $limiter->incrementRequestCount();
        $this->assertSame('3', $limiter->getClient()->get($limiter->getStorageKey()));
    }

    /**
     * Asserts that getIdentifier returns the value passed to the constructor.
     */
    public function testGetIdentifierReturnsCorrectValue(): void
    {
        $limiter = new Limiter($this->client, 'custom-identifier');

        $this->assertSame('custom-identifier', $limiter->getIdentifier());
    }

    /**
     * Asserts that setLimitExceededHandler stores and returns the given handler via getLimitExceededHandler.
     */
    public function testSetLimitExceededHandlerStoresAndReturnsHandler(): void
    {
        $handler = static function(): void {
        };

        $limiter = new Limiter($this->client, 'test-set-limit-exceeded-handler');
        $limiter->setLimitExceededHandler($handler);

        $this->assertSame($handler, $limiter->getLimitExceededHandler());
    }

    /**
     * Asserts that setRateLimit stores the requests and perSecond values correctly.
     */
    public function testSetRateLimitStoresCorrectValues(): void
    {
        $limiter = new Limiter($this->client, 'test-set-rate-limit');
        $limiter->setRateLimit(10, 20);

        $this->assertSame(10, $limiter->getRequests());
        $this->assertSame(20, $limiter->getPerSecond());
    }

    /**
     * Asserts that setStorageKey formats the storage key correctly using the given identifier.
     */
    public function testSetStorageKeyFormatsKeyWithIdentifier(): void
    {
        $limiter = new Limiter($this->client, 'test-set-storage-key');
        $limiter->setStorageKey('api:limit:%s');

        $this->assertSame('api:limit:test-set-storage-key', $limiter->getStorageKey());
    }
}
