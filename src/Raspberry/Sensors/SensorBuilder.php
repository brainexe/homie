<?php

namespace Raspberry\Sensors;

use BrainExe\Annotations\Annotations\Service;
use InvalidArgumentException;
use Raspberry\Sensors\Formatter\Formatter;
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
        $this->addDefinition($type, $sensor->getDefinition());
    }

    /**
     * @param string $type
     * @param Definition $definition
     */
    public function addDefinition($type, Definition $definition)
    {
        $this->definitions[$type] = $definition;
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

        throw new InvalidArgumentException(sprintf('Invalid sensor type: %s', $type));
    }

    /**
     * @param string $type
     * @return Formatter
     */
    public function getFormatter($type)
    {
        if (isset($this->formatter[$type])) {
            return $this->formatter[$type];
        }

        if (isset($this->sensors[$type])) {
            $type = $this->build($type)->getDefinition()->formatter;
            return $this->getFormatter($type);
        }

        return $this->getFormatter(Definition::TYPE_NONE);
    }
}
