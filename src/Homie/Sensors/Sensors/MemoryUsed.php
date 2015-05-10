<?php

namespace Homie\Sensors\Sensors;

use Homie\Sensors\CompilerPass\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Bytes;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.MemoryUsed")
 */
class MemoryUsed extends AbstractSensor
{

    const MEMINFO = '/proc/meminfo';
    const TYPE    = 'memory_used';

    /**
     * {@inheritdoc}
     */
    public function getValue($parameter)
    {
        $content = file_get_contents(self::MEMINFO);

        preg_match('/MemTotal:\s*(\d+) kB/', $content, $total);
        preg_match('/(MemAvailable|MemFree):\s*(\d+) kB/', $content, $available);

        $usedkb = $total[1] - $available[1];

        return $usedkb * 1000; // -> Bytes
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        return is_file(self::MEMINFO);
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = gettext('Memory');
        $definition->type      = Definition::TYPE_MEMORY;
        $definition->formatter = Bytes::TYPE;

        return $definition;
    }
}
