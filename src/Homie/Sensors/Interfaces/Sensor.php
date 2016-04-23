<?php

namespace Homie\Sensors\Interfaces;

use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\SensorVO;
use JsonSerializable;
use Homie\Sensors\Definition;
use Symfony\Component\Console\Output\OutputInterface;

interface Sensor extends JsonSerializable
{

    /**
     * @return string
     */
    public function getSensorType() : string;

    /**
     * @todo return typehint: float
     * @param SensorVO $sensor
     * @throws InvalidSensorValueException
     * @return float
     */
    public function getValue(SensorVO $sensor);

    /**
     * @param SensorVO $sensor
     * @throws InvalidSensorValueException
     * @return bool
     */
    public function isSupported(SensorVO $sensor) : bool;

    /**
     * @return Definition
     */
    public function getDefinition() : Definition;
}
