<?php

namespace AndrewDyer\PredisRequestLimiter\Tests\Support;

use Predis\ClientInterface;
use Predis\Command\CommandInterface;

/**
 * Simulates a Predis client using in-memory storage for testing.
 */
class FakeClient implements ClientInterface
{
    /**
     * In-memory key-value store used to simulate Redis state.
     *
     * @var array<string, int|string>
     */
    private array $storage = [];

    /**
     * Handles dynamic method calls by dispatching to in-memory Redis command simulations.
     *
     * @param string $method The command name.
     * @param array<mixed> $arguments The command arguments.
     * @return mixed The result of the simulated command.
     */
    public function __call($method, $arguments)
    {
        return match (strtolower($method)) {
            'get' => isset($this->storage[$arguments[0]])
            ? (string) $this->storage[$arguments[0]]
            : null,
            'incr' => $this->storage[$arguments[0]] = ($this->storage[$arguments[0]] ?? 0) + 1,
            'expire' => 1,
            'flushall' => (function () {
                    $this->storage = [];

                    return 'OK';
                })(),
            default => null,
        };
    }

    /**
     * Returns the command factory.
     *
     * @return null
     */
    public function getCommandFactory()
    {
        return null;
    }

    /**
     * Returns the client options.
     *
     * @return null
     */
    public function getOptions()
    {
        return null;
    }

    /**
     * Handles opening a connection to the server.
     */
    public function connect()
    {
    }

    /**
     * Handles closing the connection to the server.
     */
    public function disconnect()
    {
    }

    /**
     * Returns the underlying connection instance.
     *
     * @return null
     */
    public function getConnection()
    {
        return null;
    }

    /**
     * Creates a command instance for the given method.
     *
     * @param string $method The command name.
     * @param array<mixed> $arguments The command arguments.
     * @return null
     */
    public function createCommand($method, $arguments = [])
    {
        return null;
    }

    /**
     * Handles executing a command against the server.
     *
     * @param CommandInterface $command The command to execute.
     * @return null
     */
    public function executeCommand(CommandInterface $command)
    {
        return null;
    }
}
