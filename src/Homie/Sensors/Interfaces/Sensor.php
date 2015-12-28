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
    public function getSensorType();

    /**
     * @param SensorVO $sensor
     * @return float
     */
    public function getValue(SensorVO $sensor);

    /**
     * @param SensorVO $sensor
     * @param OutputInterface $output
     * @return bool
     */
    public function isSupported(SensorVO $sensor, OutputInterface $output);

    /**
     * @return Definition
     */
    public function getDefinition();
}
