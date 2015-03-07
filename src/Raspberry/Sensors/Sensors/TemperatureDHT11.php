<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\Annotation\Sensor;

/**
 * @Sensor("Sensor.DHT11.Temperature")
 */
class TemperatureDHT11 extends AbstractDHT11
{

    const TYPE = 'temp_dht11';

    use TemperatureTrait;

    /**
     * @return string
     */
    public function getSensorType()
    {
        return self::TYPE;
    }

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
}
