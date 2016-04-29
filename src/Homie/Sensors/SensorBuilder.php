<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;
use InvalidArgumentException;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Interfaces\Sensor;

/**
 * @Service("SensorBuilder", public=false)
 */
class SensorBuilder
{

    /**
     * @var Sensor[]
     */
    private $sensors;

    /**
     * @var Formatter[]
     */
    private $formatter = [];

    /**
     * @var Definition[]
     */
    private $definitions = [];

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
     * @todo lazy load sensors
     */
    public function addSensor(string $type, Sensor $sensor)
    {
        $this->sensors[$type] = $sensor;
    }

    /**
     * @param string $type
     * @param Formatter $formatter
     */
    public function addFormatter(string $type, Formatter $formatter)
    {
        $this->formatter[$type] = $formatter;
    }

    /**
     * @param string $type
     * @throws InvalidArgumentException
     * @return Sensor
     */
    public function build(string $type) : Sensor
    {
        if (!empty($this->sensors[$type])) {
            return $this->sensors[$type];
        }

        throw new InvalidArgumentException(sprintf('Invalid sensor type: %s', $type));
    }

    /**
     * @param string $type
     * @throws InvalidArgumentException
     * @return Definition
     */
    public function getDefinition(string $type) : Definition
    {
        if (!empty($this->definitions[$type])) {
            return $this->definitions[$type];
        }

        return $this->definitions[$type] = $this->build($type)->getDefinition();
    }

    /**
     * @param string $formatterType
     * @return Formatter
     */
    public function getFormatter(string $formatterType) : Formatter
    {
        if (isset($this->formatter[$formatterType])) {
            return $this->formatter[$formatterType];
        }

        return $this->getFormatter(Definition::TYPE_NONE);
    }

    /**
     * @return string[]
     */
    public function getFormatters() : array
    {
        return array_keys($this->formatter);
    }
}
