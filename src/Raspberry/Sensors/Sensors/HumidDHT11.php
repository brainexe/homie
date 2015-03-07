<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\Annotation\Sensor;

/**
 * @Sensor("Sensor.HumidDHT11")
 */
class HumidDHT11 extends AbstractDHT11
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
