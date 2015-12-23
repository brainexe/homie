<?php

namespace Homie\Sensors\Sensors\System;

use BrainExe\Core\Traits\RedisTrait;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Sensors\AbstractSensor;
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
    public function getValue($parameter)
    {
        list ($section, $key) = explode('.', $parameter, 2);

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
    public function isSupported($parameter, OutputInterface $output)
    {
        if ($this->getValue($parameter) === null) {
            $output->writeln(
                sprintf(
                    'Invalid stats key: "%s". Use "section.key", e.g. "memory.used_memory"',
                    $parameter
                )
            );
            return false;
        }
        return true;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = gettext('Redis');
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}
