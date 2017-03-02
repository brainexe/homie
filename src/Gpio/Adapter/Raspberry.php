<?php

namespace Homie\Gpio\Adapter;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;
use Exception;
use Generator;
use Homie\Client\ClientInterface;
use Homie\Gpio\Adapter;
use Homie\Gpio\Pin;
use Homie\Gpio\PinsCollection;

/**
 * @Service
 */
class Raspberry extends Adapter
{
    private const GPIO_COMMAND_READALL   = '%s readall';
    private const GPIO_COMMAND_DIRECTION = '%s mode %d %s';
    private const GPIO_COMMAND_VALUE     = '%s write %d %d';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var PinsCollection
     */
    private $pins;

    /**
     * @var string
     */
    private $gpioExecutable;

    /**
     * @Inject({
     *     "client"         = "@HomieClient",
     *     "gpioExecutable" = "%gpio.command%"
     * })
     * @param ClientInterface $client
     * @param string $gpioExecutable
     */
    public function __construct(ClientInterface $client, string $gpioExecutable)
    {
        $this->client         = $client;
        $this->gpioExecutable = $gpioExecutable;
    }

    /**
     * @return PinsCollection
     */
    public function loadPins() : PinsCollection
    {
        if (null !== $this->pins) {
            return $this->pins;
        }

        $pins = $this->parsePins();
        foreach ($pins as $r) {
            $matches = array_map('trim', $r);

            $pin = new Pin();
            $pin->setSoftwareId((int)$matches[1]);
            $pin->setName($matches[2]);
            $pin->setMode($matches[3]);
            $pin->setValue((bool)$matches[4]);
            $pin->setPhysicalId((int)$matches[5]);

            $this->pins->add($pin);
        }

        return $this->pins;
    }

    /**
     * @return Generator
     */
    protected function parsePins()
    {
        $lines      = explode("\n", $this->loadFile());
        $type       = trim($lines[0], ' +-');
        $this->pins = new PinsCollection($type);

        $lines = array_slice($lines, 3, -4);

        foreach ($lines as $line) {
            $line = substr($line, 2, -1);

            [$part1, $part2] = explode('||', $line);
            $part1 = explode('|', $part1);
            $part2 = array_reverse(explode('|', $part2));

            yield $part1;
            yield $part2;
        }
    }

    /**
     * @return string
     */
    protected function loadFile() : string
    {
        try {
            $command = sprintf(self::GPIO_COMMAND_READALL, $this->gpioExecutable);
            return $this->client->executeWithReturn($command);
        } catch (Exception $e) {
            return file_get_contents(__DIR__ . '/raspberry.txt');
        }
    }

    /**
     * @param Pin $pin Pin
     */
    public function updatePin(Pin $pin) : void
    {
        $pinValue = $pin->getValue();

        $command = sprintf(
            self::GPIO_COMMAND_DIRECTION,
            $this->gpioExecutable,
            $pin->getPhysicalId(),
            escapeshellarg($pin->getMode())
        );
        $this->client->execute($command);

        $command = sprintf(
            self::GPIO_COMMAND_VALUE,
            $this->gpioExecutable,
            $pin->getPhysicalId(),
            $pinValue
        );
        $this->client->execute($command);
    }
}
