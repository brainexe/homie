<?php

namespace Homie\Sensors\Sensors\System;

use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Bytes;
use Homie\Sensors\Sensors\AbstractSensor;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Annotations\Annotations\Inject;

/**
 * @Sensor("Sensor.System.DiskUsed")
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
    public function getValue($parameter)
    {
        $content = $this->client->executeWithReturn('df . --output=used');

        if (preg_match('/(\d+)/', $content, $matches)) {
            return (int)$matches[1] * 1000; // -> we get kb
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        return true;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_DISK;
        $definition->formatter = Bytes::TYPE;

        return $definition;
    }
}
