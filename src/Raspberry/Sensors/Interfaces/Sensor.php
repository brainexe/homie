<?php

namespace Raspberry\Sensors\Interfaces;

use Symfony\Component\Console\Output\OutputInterface;

interface Sensor
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
     * @param double $value
     * @return string
     */
    public function formatValue($value);

    /**
     * @param float $value
     * @return string|null
     */
    public function getEspeakText($value);

    /**
     * @param string $parameter
     * @param OutputInterface $output
     * @return bool
     */
    public function isSupported($parameter, OutputInterface $output);
}
