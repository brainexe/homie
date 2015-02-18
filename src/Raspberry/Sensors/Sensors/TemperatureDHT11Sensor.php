<?php

namespace Raspberry\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service(public=false, tags={{"name" = "sensor"}})
 */
class TemperatureDHT11Sensor extends AbstractDHT11Sensor
{

    const TYPE = 'temp_dht11';

    use TemperatureSensorTrait;

    /**
     * @return string
     */
    public function getSensorType()
    {
        return self::TYPE;
    }

    /**
     * @param integer $pin
     * @return double
     */
    public function getValue($pin)
    {
        $output = $this->getContent($pin);

        if (!preg_match('/Temp = (\d+) /', $output, $matches)) {
            return null;
        }

        return (double)$matches[1];
    }
}
