<?php

namespace Homie\Sensors\Sensors\Humid;

use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\Percentage;
use Homie\Sensors\Sensors\AbstractDHT11;
use Homie\Sensors\SensorVO;

/**
 * DHT11 / DHT22
 * @Sensor("Sensor.Humid.DHT11")
 * @source https://klenzel.de/1827
 * @source http://www.messtechniklabor.de/artikel-h0000-temperatur_und_luftfeuchtigkeit_messen.html
 */
class DHT11 extends AbstractDHT11
{

    const TYPE = 'humid.dht11';

    use HumidityTrait;

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        $output = $this->getContent($sensor->parameter);

        if (!preg_match('/(Hum|Humidity) = ([\d\.]+) %/', $output, $matches)) {
            throw new InvalidSensorValueException($sensor, sprintf('Invalid humidity value: %s', $output));
        }

        $humidity = $matches[2];
        if (!$this->validateHumidity($humidity)) {
            throw new InvalidSensorValueException($sensor, sprintf('Invalid humidity value: %s', $output));
        }

        return $this->round($humidity, 0.01);
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->formatter = Percentage::TYPE;
        $definition->type      = Definition::TYPE_HUMIDITY;

        return $definition;
    }
}
