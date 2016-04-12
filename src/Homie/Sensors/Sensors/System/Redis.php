<?php

namespace Homie\Sensors\Sensors\System;

use BrainExe\Core\Traits\RedisTrait;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.System.Redis")
 */
class Redis extends AbstractSensor implements Parameterized
{
    use RedisTrait;

    const TYPE = 'system.redis';

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor)
    {
        list ($section, $key) = explode('.', $sensor->parameter, 2);

        $section = ucfirst($section);
        $data = $this->getRedis()->info($section);

        if (empty($data) || !array_key_exists($key, $data[$section])) {
            return null;
        }

        return $data[$section][$key];
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor, OutputInterface $output) : bool
    {
        if ($this->getValue($sensor) === null) {
            $output->writeln(
                sprintf(
                    'Invalid stats key: "%s". Use "section.key", e.g. "memory.used_memory"',
                    $sensor->parameter
                )
            );
            return false;
        }
        return true;
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}
