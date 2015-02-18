<?php

namespace Raspberry\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service(public=false, tags={{"name" = "sensor"}})
 */
class HumidDHT11Sensor extends AbstractDHT11Sensor
{

    const TYPE = 'humid_dht11';

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

        if (!preg_match('/Hum = (\d+) %/', $output, $matches)) {
            return null;
        }

        return (double)$matches[1];
    }

    /**
     * @param double $value
     * @return string
     */
    public function formatValue($value)
    {
        return sprintf('%d%%', $value);
    }

    /**
     * @param float $value
     * @return string|null
     */
    public function getEspeakText($value)
    {
        return sprintf(_('%d Percent'), $value);
    }
}
