<?php

namespace Homie\Sensors\Sensors\Temperature;

use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\Temperature;
use Homie\Sensors\Sensors\AbstractDHT11;
use Homie\Sensors\SensorVO;

/**
 * @Sensor("Sensor.DHT11.Temperature")
 */
class DHT11 extends AbstractDHT11
{

    use TemperatureTrait;

    const TYPE = 'temperature.dht11';

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        $output = $this->getContent($sensor->parameter);

        if (!preg_match('/Temperature = ([-\d.]+)/', $output, $matches)) {
            throw new InvalidSensorValueException($sensor, sprintf('Invalid value: %s', $output));
        }

        $temperature = $this->round($matches[1], 0.01);
        if (!$this->validateTemperature($temperature)) {
            throw new InvalidSensorValueException($sensor, sprintf('Invalid value: %s', $output));
        }

        return $temperature;
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_TEMPERATURE;
        $definition->formatter = Temperature::TYPE;

        return $definition;
    }
}
