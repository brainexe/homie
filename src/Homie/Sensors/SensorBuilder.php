<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use InvalidArgumentException;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Interfaces\Sensor;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @Service("SensorBuilder")
 */
class SensorBuilder
{

    /**
     * @var Sensor[]
     */
    private $sensors;

    /**
     * @var string[]
     */
    private $sensorTypes = [];

    /**
     * @var Formatter[]
     */
    private $formatter = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @Inject({"@service_container"})
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return Sensor[]
     */
    public function getSensors()
    {
        $keys = array_keys($this->sensorTypes);

        return array_map([$this, 'build'], array_combine($keys, $keys));
    }

    /**
     * @param string $type
     * @param string $serviceId
     */
    public function addSensor(string $type, string $serviceId)
    {
        $this->sensorTypes[$type] = $serviceId;
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

        if (!empty($this->sensorTypes[$type])) {
            /** @var Sensor $sensor */
            $sensor = $this->container->get($this->sensorTypes[$type]);
            return $this->sensors[$type] = $sensor;
        }

        throw new InvalidArgumentException(sprintf('Invalid sensor type: %s', $type));
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
