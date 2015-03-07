<?php

namespace Raspberry\Sensors;

use BrainExe\Annotations\Annotations\Service;
use InvalidArgumentException;
use Raspberry\Sensors\Interfaces\Sensor;

/**
 * @Service(public=false)
 */
class SensorBuilder
{

    /**
     * @var Sensor[]
     */
    private $sensors;

    /**
     * @return Sensor[]
     */
    public function getSensors()
    {
        return $this->sensors;
    }

    /**
     * @param string $type
     * @param Sensor $sensor
     */
    public function addSensor($type, Sensor $sensor)
    {
        $this->sensors[$type] = $sensor;
    }

    /**
     * @param string $type
     * @throws InvalidArgumentException
     * @return Sensor
     */
    public function build($type)
    {
        if (!empty($this->sensors[$type])) {
            return $this->sensors[$type];
        }

        throw new InvalidArgumentException(sprintf('Invalid sensor type: %s', $type));
    }
}
