<?php

namespace Raspberry\Sensors\Interfaces;

use JsonSerializable;
use Raspberry\Sensors\Definition;
use Symfony\Component\Console\Output\OutputInterface;

interface Sensor extends JsonSerializable
{

    /**
     * @return string
     */
    public function getSensorType();

    /**
     * @param integer $parameter
     * @return double
     */
    public function getValue($parameter);

    /**
     * @param string $parameter
     * @param OutputInterface $output
     * @return bool
     */
    public function isSupported($parameter, OutputInterface $output);

    /**
     * @return Definition
     */
    public function getDefinition();
}
