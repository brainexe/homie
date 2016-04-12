<?php

namespace Homie\Sensors\Interfaces;

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
     * @todo throw more exceptions in case of error
     * @param SensorVO $sensor
     * @return float
     */
    public function getValue(SensorVO $sensor);

    /**
     * @param SensorVO $sensor
     * @param OutputInterface $output
     * @return bool
     */
    public function isSupported(SensorVO $sensor, OutputInterface $output) : bool;

    /**
     * @return Definition
     */
    public function getDefinition() : Definition;
}
