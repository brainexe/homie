<?php

namespace Homie\Sensors\Sensors\Misc;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Misc.Script")
 */
class Script extends AbstractSensor implements Parameterized
{

    const TYPE = 'custom.script';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject({"@HomieClient"})
     * @param ClientInterface $client
     */
    public function __construct(
        ClientInterface $client
    ) {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor)
    {
        return (float)$this->client->executeWithReturn((string)$sensor->parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor, OutputInterface $output) : bool
    {
        $current = $this->getValue($sensor);

        return $current !== null;
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}
