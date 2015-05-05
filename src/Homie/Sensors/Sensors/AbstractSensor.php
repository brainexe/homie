<?php

namespace Homie\Sensors\Sensors;

use Homie\Sensors\Definition;
use Homie\Sensors\Interfaces\Sensor;

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
