<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter("Formatter.Percentage")
 */
class Percentage extends Formatter
{
    const TYPE = 'percentage';

    /**
     * @param double $value
     * @return string
     */
    public function formatValue($value)
    {
        return sprintf('%d%%', $value);
    }

    /**
     * @param float $value
     * @return string|null
     */
    public function getEspeakText($value)
    {
        return sprintf(_('%d Percent'), $value);
    }
}
