<?php

namespace Homie\Sensors\Sensors;

use BrainExe\Core\Translation\TranslationProvider;
use Homie\Sensors\Definition;
use Homie\Sensors\Interfaces\Sensor;

abstract class AbstractSensor implements Sensor, TranslationProvider
{
    const TYPE = 'unknown';

    const TOKEN_NAME = 'sensor.%s.name';

    /**
     * @return string
     */
    public function getSensorType()
    {
        return static::TYPE;
    }

    /**
     * @return Definition
     */
    public function jsonSerialize()
    {
        return $this->getDefinition();
    }

    /**
     * @param float $value
     * @param int $multiplier
     * @return float
     */
    protected function round($value, $multiplier = 1)
    {
        return (int)($value / $multiplier) * $multiplier;
    }

    /**
     * @return string[]
     */
    public static function getTokens()
    {
        yield sprintf(self::TOKEN_NAME, static::TYPE);
    }
}
