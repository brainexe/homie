<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\CompilerPass\Annotation\Sensor;
use Raspberry\Sensors\Definition;
use Raspberry\Sensors\Formatter\Temperature;

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

        return (double)$matches[1];
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = _('Temperature');
        $definition->type      = Definition::TYPE_TEMPERATURE;
        $definition->formatter = Temperature::TYPE;

        return $definition;
    }
}
