<?php

namespace Homie\Sensors\Sensors\Misc;

use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\SkipSensorValueException;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;

/**
 * @Sensor
 */
class None extends AbstractSensor
{

    const TYPE = 'custom.none';

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        throw new SkipSensorValueException();
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
