<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\Annotation\Sensor;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Barometer")
 * @link https://learn.adafruit.com/using-the-bmp085-with-raspberry-pi/using-the-adafruit-bmp-python-library
 * @link http://www.adafruit.com/product/1603
 */
class Barometer implements SensorInterface
{

    const TYPE = 'barometer';

    /**
     * @param integer $pin
     * @return double
     */
    public function getValue($pin)
    {
        unset($pin);
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(OutputInterface $output)
    {
        return true;
    }

    /**
     * @return string
     */
    public function getSensorType()
    {
        return self::TYPE;
    }

    /**
     * @param double $value
     * @return string
     */
    public function formatValue($value)
    {
        return (string)$value;
    }

    /**
     * @param float $value
     * @return string|null
     */
    public function getEspeakText($value)
    {
        return $this->formatValue($value);
    }
}
