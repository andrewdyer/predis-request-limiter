<?php

namespace Anddye\PredisRequestLimiter\Tests;

use Anddye\PredisRequestLimiter\Limiter;
use Anddye\PredisRequestLimiter\Tests\Support\FakeClient;
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
     * Asserts that the default limit exceeded handler is returned when no custom handler has been set.
     */
    public function testDefaultLimitExceededHandler(): void
    {
        $limiter = new Limiter($this->client, 'test-default-limit-exceeded-handler');

        $this->assertEquals($limiter->defaultLimitExceededHandler(), $limiter->getLimitExceededHandler());
    }

    /**
     * Asserts that hasExceededRateLimit returns true once the request count reaches the configured limit.
     */
    public function testHasExceededRateLimit(): void
    {
        $limiter = new Limiter($this->client, 'test-has-exceeded-rate-limit');
        $limiter->setRateLimit(3, 30);

        $limiter->incrementRequestCount();
        $this->assertFalse($limiter->hasExceededRateLimit());

        $limiter->incrementRequestCount();
        $this->assertFalse($limiter->hasExceededRateLimit());

        $limiter->incrementRequestCount();
        $this->assertTrue($limiter->hasExceededRateLimit());
    }

    /**
     * Asserts that incrementing the request count correctly updates the stored value.
     */
    public function testIncrementRequestCount(): void
    {
        $limiter = new Limiter($this->client, 'test-increment-request-count');

        $limiter->incrementRequestCount();
        $this->assertEquals('1', $limiter->getClient()->get($limiter->getStorageKey()));

        $limiter->incrementRequestCount();
        $this->assertEquals('2', $limiter->getClient()->get($limiter->getStorageKey()));

        $limiter->incrementRequestCount();
        $this->assertEquals('3', $limiter->getClient()->get($limiter->getStorageKey()));
    }

    /**
     * Asserts that the identifier is stored and returned correctly.
     */
    public function testSetIdentifier(): void
    {
        $identifier = 'custom identifier';

        $limiter = new Limiter($this->client, $identifier);

        $this->assertEquals($identifier, $limiter->getIdentifier());
    }

    /**
     * Asserts that a custom limit exceeded handler is stored and returned correctly.
     */
    public function testSetLimitExceededHandler(): void
    {
        $handler = function() {
        };

        $limiter = new Limiter($this->client, 'test-set-limit-exceeded-handler');
        $limiter->setLimitExceededHandler($handler);

        $this->assertEquals($handler, $limiter->getLimitExceededHandler());
    }

    /**
     * Asserts that the rate limit values are stored and returned correctly.
     */
    public function testSetRateLimit(): void
    {
        $requests = 10;
        $perSecond = 20;

        $limiter = new Limiter($this->client, 'test-set-rate-limit');
        $limiter->setRateLimit($requests, $perSecond);

        $this->assertEquals($requests, $limiter->getRequests());
        $this->assertEquals($perSecond, $limiter->getPerSecond());
    }

    /**
     * Asserts that the storage key is formatted correctly using the given identifier.
     */
    public function testSetStorageKey(): void
    {
        $identifier = 'test-set-storage-key';
        $storageKey = 'api:limit:%s';

        $limiter = new Limiter($this->client, $identifier);
        $limiter->setStorageKey($storageKey);

        $this->assertEquals('api:limit:test-set-storage-key', $limiter->getStorageKey());
    }
}
