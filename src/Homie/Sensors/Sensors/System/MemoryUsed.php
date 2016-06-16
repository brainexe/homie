<?php

namespace Homie\Sensors\Sensors\System;

use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Bytes;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;

/**
 * @Sensor("Sensor.System.MemoryUsed")
 */
class MemoryUsed extends AbstractSensor
{
    const TYPE    = 'system.memory_used';
    const MEMINFO = '/proc/meminfo';

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        $content = file_get_contents(self::MEMINFO);

        preg_match('/MemTotal:\s*(\d+) kB/', $content, $total);
        preg_match('/MemAvailable:\s*(\d+) kB/', $content, $available);

        $usedkb = @$total[1] - @$available[1];

        return $usedkb * 1000; // -> Bytes
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor) : bool
    {
        return is_file(self::MEMINFO);
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_MEMORY;
        $definition->formatter = Bytes::TYPE;

        return $definition;
    }
}
