<?php

namespace Homie\Gpio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use Exception;
use Generator;
use Homie\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class PinLoader
{

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var PinsCollection
     */
    private $pins = null;

    /**
     * @var string
     */
    private $gpioExecutable;

    /**
     * @Inject({"@HomieClient", "%gpio.command%"})
     * @param ClientInterface $client
     * @param string $gpioExecutable
     */
    public function __construct(ClientInterface $client, $gpioExecutable)
    {
        $this->client = $client;
        $this->gpioExecutable = $gpioExecutable;
    }

    /**
     * @param string $pin
     * @return Pin
     */
    public function loadPin($pin)
    {
        $pins = $this->loadPins();

        return $pins->getByWiringId($pin);
    }

    /**
     * @return PinsCollection
     */
    public function loadPins()
    {
        if (null !== $this->pins) {
            return $this->pins;
        }

        $pins = $this->parsePins();
        foreach ($pins as $r) {
            $matches = array_map('trim', $r);

            $pin = new Pin();
            $pin->setWiringId((int)$matches[1]);
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

            list($part1, $part2) = explode('||', $line);
            $part1 = explode('|', $part1);
            $part2 = array_reverse(explode('|', $part2));

            yield $part1;
            yield $part2;
        }
    }

    /**
     * @return string
     */
    protected function loadFile()
    {
        try {
            $command = sprintf(GpioManager::GPIO_COMMAND_READALL, $this->gpioExecutable);
            return $this->client->executeWithReturn($command);
        } catch (Exception $e) {
            return file_get_contents(__DIR__ . '/gpio.txt');
        }
    }
}
