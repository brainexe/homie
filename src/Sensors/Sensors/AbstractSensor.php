<?php

namespace Homie\Sensors\Sensors;

use BrainExe\Core\Translation\TranslationProvider;
use Generator;
use Homie\Sensors\Definition;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorVO;

abstract class AbstractSensor implements Sensor, TranslationProvider
{
    const TYPE = 'unknown';

    private const TOKEN_NAME = 'sensor.%s.name';

    /**
     * @return string
     */
    public function getSensorType() : string
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
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor) : bool
    {
        // try to fetch current value. In case of error InvalidSensorValueException, just pass it trough
        $this->getValue($sensor);

        return true;
    }

    /**
     * @param float $value
     * @param int $multiplier
     * @return float
     */
    protected function round($value, $multiplier = 1) : float
    {
        return (int)($value / $multiplier) * $multiplier;
    }

    /**
     * @return string[]|Generator
     */
    public static function getTokens()
    {
        yield sprintf(self::TOKEN_NAME, static::TYPE);
    }
}
