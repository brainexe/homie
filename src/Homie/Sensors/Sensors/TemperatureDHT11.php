<?php

namespace Homie\Sensors\Sensors;

use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Temperature;

/**
 * @Sensor("Sensor.DHT11.Temperature")
 */
class TemperatureDHT11 extends AbstractDHT11
{

    const TYPE = 'temp_dht11';

    /**
     * @param integer $parameter
     * @return double
     */
    public function getValue($parameter)
    {
        $output = $this->getContent($parameter);

        if (!preg_match('/Temp = (\d+) /', $output, $matches)) {
            return null;
        }

        return round($matches[1], 1);
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = gettext('Temperature');
        $definition->type      = Definition::TYPE_TEMPERATURE;
        $definition->formatter = Temperature::TYPE;

        return $definition;
    }
}
