<?php

namespace Homie\Sensors\Sensors\Misc;

use Homie\Node;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\SensorVO;

/**
 * @Sensor("Sensor.Misc.Particle")
 */
class Particle extends Expression implements Parameterized
{
    const TYPE = 'custom.particle';

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        $tmpSensor = new SensorVO();
        $tmpSensor->node = $sensor->node;
        $tmpSensor->parameter = sprintf(
            'callParticleFunction(nodeId, "%s")',
            $sensor->parameter
        );

        $value = parent::getPlainValue($tmpSensor);

        if (strpos($value, 'error') !== false) {
            throw new InvalidSensorValueException($sensor, $value);
        }

        if ($value > 1000000) {
            $value /= 1000000;
        }

        return $value;
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
        $definition->requiredNode = [Node::TYPE_PARTICLE];

        return $definition;
    }
}
