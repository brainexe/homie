<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\Definition;
use Raspberry\Sensors\Interfaces\Sensor;

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
}
