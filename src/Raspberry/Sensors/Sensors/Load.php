<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\Annotation\Sensor;
use Symfony\Component\Console\Output\OutputInterface;
use Raspberry\Sensors\Interfaces\Sensor as SensorInterface;

/**
 * @Sensor("Sensor.Load")
 */
class Load implements SensorInterface
{

    const TYPE = 'load';

    /**
     * {@inheritdoc}
     */
    public function getSensorType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($parameter)
    {
        unset($parameter);

        return sys_getloadavg()[0];
    }

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return sprintf('%0.1f', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEspeakText($value)
    {
        return sprintf('%1.1f', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        return true;
    }
}
