<?php

namespace Homie\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\SensorVO;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @link http://www.adafruit.com/product/386
 * @link https://learn.adafruit.com/dht
 */
abstract class AbstractDHT11 extends AbstractSensor implements Parameterized
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @Inject({"@HomieClient", "@FileSystem"})
     * @param ClientInterface $client
     * @param Filesystem $filesystem
     */
    public function __construct(
        ClientInterface $client,
        Filesystem $filesystem
    ) {
        $this->client     = $client;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $parameter
     * @return string
     */
    protected function getContent(string $parameter) : string
    {
        $command = sprintf('timeout 5 %s', $parameter);

        return $this->client->executeWithReturn($command);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor) : bool
    {
        $file = explode(' ', $sensor->parameter)[0];

        if (!$this->filesystem->exists($file)) {
            $message = sprintf(
                '%s: Script not exists: %s',
                $this->getSensorType(),
                $sensor->parameter
            );

            throw new InvalidSensorValueException($sensor, $message);
        }

        return true;
    }
}
