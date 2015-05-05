<?php

namespace Homie\Sensors\Formatter;

interface Formatter
{

    /**
     * @return string
     */
    public function getType();

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
}
