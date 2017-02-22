<?php

namespace Homie\Sensors\Sensors\System;

use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\Percentage;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;
use BrainExe\Core\Annotations\Inject;

/**
 * @Sensor("Sensor.System.DiskUsedPercent")
 */
class DiskUsedPercent extends AbstractSensor
{

    const TYPE = 'system.disk_used_percent';

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
        $content = $this->client->executeWithReturn('df .');

        if (preg_match('/\s(\d+)%/', $content, $matches)) {
            return (float)$matches[1];
        }

        throw new InvalidSensorValueException($sensor, sprintf('No disk value found: %s', $content));
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor) : bool
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
        $definition->formatter = Percentage::TYPE;

        return $definition;
    }
}
