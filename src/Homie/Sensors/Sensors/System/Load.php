<?php

namespace Homie\Sensors\Sensors\System;

use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Load as Formatter;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;

/**
 * @Sensor("Sensor.System.Load")
 */
class Load extends AbstractSensor
{

    const TYPE = 'system.load';

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor)
    {
        unset($sensor);

        return sys_getloadavg()[0];
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
        $definition = new Definition();
        $definition->type       = Definition::TYPE_LOAD;
        $definition->formatter  = Formatter::TYPE;

        return $definition;
    }
}
