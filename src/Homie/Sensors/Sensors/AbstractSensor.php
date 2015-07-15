<?php

namespace Homie\Sensors\Sensors;

use Homie\Sensors\Definition;
use Homie\Sensors\Interfaces\Sensor;

/**
 * @todo check if package is installed via "dpkg-query -W -f='${Status} ${Version}\n' $package"
 */
abstract class AbstractSensor implements Sensor
{

    const TYPE = 'unknown';

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
}
