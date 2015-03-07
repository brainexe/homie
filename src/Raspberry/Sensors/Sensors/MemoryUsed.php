<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\Annotation\Sensor;
use Symfony\Component\Console\Output\OutputInterface;
use Raspberry\Sensors\Interfaces\Sensor as SensorInterface;

/**
 * @Sensor("Sensor.MemoryUsed")
 */
class MemoryUsed implements SensorInterface
{

    const MEMINFO = '/proc/meminfo';
    const TYPE    = 'memory_used';

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
        $content = file_get_contents(self::MEMINFO);

        preg_match('/MemTotal:\s*(\d+) kB/', $content, $total);
        preg_match('/MemAvailable:\s*(\d+) kB/', $content, $available);

        $usedkb = $total[1] - $available[1];

        return $usedkb / 1000;
    }

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return sprintf('%9.1fMB', $value);
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
        return is_file(self::MEMINFO);
    }
}
