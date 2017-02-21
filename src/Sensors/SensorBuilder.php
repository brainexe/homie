<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Interfaces\Sensor;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @Service
 */
class SensorBuilder
{
    /**
     * @var Formatter[]
     */
    private $formatter = [];

    /**
     * @var ServiceLocator
     */
    private $sensors;
    /**
     * @var array
     */
    private $sensorIds;

    /**
     * @param ServiceLocator $sensors
     * @param array[] $sensorIds Todo: replace when ServiceLocator->serviceIds() is available
     */
    public function __construct(ServiceLocator $sensors, array $sensorIds)
    {
        $this->sensors   = $sensors;
        $this->sensorIds = $sensorIds;
    }

    /**
     * @return Sensor[]
     */
    public function getSensors()
    {
        return array_map([$this, 'build'], array_combine($this->sensorIds, $this->sensorIds));
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
     * @throws ServiceNotFoundException
     * @return Sensor
     */
    public function build(string $type) : Sensor
    {
        return $this->sensors->get($type);
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
}
