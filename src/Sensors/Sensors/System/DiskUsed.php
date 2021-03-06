<?php

namespace Homie\Sensors\Sensors\System;

use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\Bytes;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;
use BrainExe\Core\Annotations\Inject;

/**
 * @Sensor
 */
class DiskUsed extends AbstractSensor
{
    const TYPE = 'system.disk_used';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject("@HomieClient")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        $content = $this->client->executeWithReturn('df', ['.', '--output=used']);

        if (preg_match('/(\d+)/', $content, $matches)) {
            return (int)$matches[1] * 1000; // -> we get kb
        }

        throw new InvalidSensorValueException($sensor, sprintf('No disk value found: %s', $content));
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensorVO) : bool
    {
        return true;
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_DISK;
        $definition->formatter = Bytes::TYPE;

        return $definition;
    }
}
