<?php

namespace Homie\Sensors\Sensors;

use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Bytes;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Annotations\Annotations\Inject;

/**
 * @Sensor("Sensor.DiskUsed")
 */
class DiskUsed extends AbstractSensor
{
    const TYPE = 'disk_used';

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
        $definition->name      = gettext('Disk used (bytes)');
        $definition->type      = Definition::TYPE_DISK;
        $definition->formatter = Bytes::TYPE;

        return $definition;
    }
}
