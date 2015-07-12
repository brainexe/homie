<?php

namespace Homie\Sensors\Sensors;

use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Percentage;

/**
 * @Sensor("Sensor.HumidDHT11")
 * @source https://klenzel.de/1827
 * @source http://www.messtechniklabor.de/artikel-h0000-temperatur_und_luftfeuchtigkeit_messen.html
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

        if (!preg_match('/(Hum|Humidity) = ([\d\.]+) %/', $output, $matches)) {
            return null;
        }

        return round($matches[2], 1);
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = gettext('Humidity');
        $definition->formatter = Percentage::TYPE;
        $definition->type      = Definition::TYPE_HUMIDITY;

        return $definition;
    }
}
