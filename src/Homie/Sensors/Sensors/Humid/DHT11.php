<?php

namespace Homie\Sensors\Sensors\Humid;

use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Percentage;
use Homie\Sensors\Sensors\AbstractDHT11;

/**
 * DHT11 / DHT22
 * @Sensor("Sensor.Humid.DHT11")
 * @source https://klenzel.de/1827
 * @source http://www.messtechniklabor.de/artikel-h0000-temperatur_und_luftfeuchtigkeit_messen.html
 */
class DHT11 extends AbstractDHT11
{

    const TYPE = 'humid.dht11';

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

        return $this->round($matches[2], 0.05);
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->formatter = Percentage::TYPE;
        $definition->type      = Definition::TYPE_HUMIDITY;

        return $definition;
    }
}
