<?php

namespace Raspberry\Sensors\Formatter;

use Raspberry\Sensors\CompilerPass\Annotation\SensorFormatter;
use Raspberry\Sensors\Definition;

/**
 * @SensorFormatter("Formatter.Percentage")
 */
class Percentage implements Formatter
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

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
