<?php

namespace Anddye\PredisRequestLimiter\Tests;

use Anddye\PredisRequestLimiter\Limiter;
use PHPUnit\Framework\TestCase;
use Predis\Client;

final class LimiterTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $parameters = [
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => '6379',
            'password' => '',
        ];

        $this->client = new Client($parameters);
        $this->client->flushall();
    }

    public function testDefaultLimitExceededHandler(): void
    {
        $limiter = new Limiter($this->client, 'test-default-limit-exceeded-handler');

        $this->assertEquals($limiter->defaultLimitExceededHandler(), $limiter->getLimitExceededHandler());
    }

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

    public function testSetIdentifier(): void
    {
        $identifier = 'custom identifier';

        $limiter = new Limiter($this->client, $identifier);

        $this->assertEquals($identifier, $limiter->getIdentifier());
    }

    public function testSetLimitExceededHandler(): void
    {
        $handler = function () {};

        $limiter = new Limiter($this->client, 'test-set-limit-exceeded-handler');
        $limiter->setLimitExceededHandler($handler);

        $this->assertEquals($handler, $limiter->getLimitExceededHandler());
    }

    public function testSetRateLimit(): void
    {
        $requests = 10;
        $perSecond = 20;

        $limiter = new Limiter($this->client, 'test-set-rate-limit');
        $limiter->setRateLimit($requests, $perSecond);

        $this->assertEquals($requests, $limiter->getRequests());
        $this->assertEquals($perSecond, $limiter->getPerSecond());
    }

    public function testSetStorageKey(): void
    {
        $identifier = 'test-set-storage-key';
        $storageKey = 'api:limit:%s';

        $limiter = new Limiter($this->client, $identifier);
        $limiter->setStorageKey($storageKey);

        $this->assertEquals('api:limit:test-set-storage-key', $limiter->getStorageKey());
    }
}
