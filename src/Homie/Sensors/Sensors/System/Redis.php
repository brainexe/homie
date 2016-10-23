<?php

namespace Homie\Sensors\Sensors\System;

use BrainExe\Core\Traits\RedisTrait;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Interfaces\Searchable;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;

/**
 * @Sensor("Sensor.System.Redis")
 */
class Redis extends AbstractSensor implements Parameterized, Searchable
{
    use RedisTrait;

    const TYPE = 'system.redis';

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        list ($section, $key) = explode('.', $sensor->parameter, 2);

        $section = ucfirst($section);
        $data = $this->getRedis()->info($section);

        if (empty($data) || !array_key_exists($key, $data[$section])) {
            throw new InvalidSensorValueException($sensor, sprintf('Not supported section: %s', $sensor->parameter));
        }

        return (float)$data[$section][$key];
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

    /**
     * @return string[]
     */
    public function search() : array
    {
        $finalValues = [];
        $data = $this->getRedis()->info();

        foreach ($data as $section => $values) {
            foreach ($values as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $finalValues[] = sprintf('%s.%s', $section, $key);
            }
        }

        return $finalValues;
    }
}
