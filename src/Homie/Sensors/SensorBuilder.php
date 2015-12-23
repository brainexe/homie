<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;
use InvalidArgumentException;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Interfaces\Sensor;

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
     */
    public function addSensor($type, Sensor $sensor)
    {
        $this->sensors[$type] = $sensor;
    }

    /**
     * @param string $type
     * @param Formatter $formatter
     */
    public function addFormatter($type, Formatter $formatter)
    {
        $this->formatter[$type] = $formatter;
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

    /**
     * @param string $type
     * @throws InvalidArgumentException
     * @return Definition
     */
    public function getDefinition($type)
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
    public function getFormatter($formatterType)
    {
        if (isset($this->formatter[$formatterType])) {
            return $this->formatter[$formatterType];
        }

        return $this->getFormatter(Definition::TYPE_NONE);
    }

    /**
     * @return string[]
     */
    public function getFormatters()
    {
        return array_keys($this->formatter);
    }
}
