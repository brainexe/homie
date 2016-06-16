<?php

namespace Homie\Sensors\Sensors\Misc;

use Homie\Node;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
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
        $tmpSensor->parameter = sprintf(
            'callParticleFunction(nodeId, "%s")',
            $sensor->parameter
        );

        $value = parent::getValue($tmpSensor);

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
