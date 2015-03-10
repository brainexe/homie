<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\CompilerPass\Annotation\Sensor;
use Raspberry\Sensors\Definition;
use Raspberry\Sensors\Formatter\Percentage;

/**
 * @Sensor("Sensor.HumidDHT11")
 */
class HumidDHT11 extends AbstractDHT11
{

    const TYPE = 'humid_dht11';

    /**
     * @param integer $parameter
     * @return double
     */
    public function getValue($parameter)
    {
        $output = $this->getContent($parameter);

        if (!preg_match('/Hum = (\d+) %/', $output, $matches)) {
            return null;
        }

        return (double)$matches[1];
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = _('Humidity');
        $definition->type      = Definition::TYPE_HUMIDITY;
        $definition->formatter = Percentage::TYPE;

        return $definition;
    }
}
