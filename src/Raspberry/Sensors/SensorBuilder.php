<?php

namespace Raspberry\Sensors;

use InvalidArgumentException;
use Raspberry\Sensors\Sensors\SensorInterface;

/**
 * @Service(public=false)
 */
class SensorBuilder
{

    /**
     * @var SensorInterface[]
     */
    private $sensors;

    /**
     * @return SensorInterface[]
     */
    public function getSensors()
    {
        return $this->sensors;
    }

    /**
     * @param string $type
     * @param SensorInterface $sensor
     */
    public function addSensor($type, SensorInterface $sensor)
    {
        $this->sensors[$type] = $sensor;
    }

    /**
     * @param string $type
     * @throws InvalidArgumentException
     * @return SensorInterface
     */
    public function build($type)
    {
        if (!empty($this->sensors[$type])) {
            return $this->sensors[$type];
        }

        throw new InvalidArgumentException(sprintf('Invalid sensor type: %s', $type));
    }
}
