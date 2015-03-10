<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\CompilerPass\Annotation\Sensor;
use Raspberry\Sensors\Definition;
use Raspberry\Sensors\Formatter\Bytes;
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
        preg_match('/MemAvailable:\s*(\d+) kB/', $content, $available);

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
        $definition->name      = _('Memory');
        $definition->type      = Definition::TYPE_MEMORY;
        $definition->formatter = Bytes::TYPE;

        return $definition;
    }
}
