<?php

namespace Homie\Sensors\Sensors\Misc;

use BrainExe\Core\Annotations\Inject;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\Aggregate\Aggregated;
use Homie\Sensors\SensorVO;

/**
 * @Sensor("Sensor.Custom.Aggregator")
 */
class Aggregator extends AbstractSensor implements Parameterized
{

    const TYPE = 'custom.aggregator';

    /**
     * @var Aggregated
     */
    private $aggregated;

    /**
     * @Inject({"@Sensor.Sensor.Aggregated.Aggregated"})
     * @param Aggregated $aggregated
     */
    public function __construct(
        Aggregated $aggregated
    ) {
        $this->aggregated = $aggregated;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        return (float)$this->aggregated->getCurrent($sensor->parameter);
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
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}
