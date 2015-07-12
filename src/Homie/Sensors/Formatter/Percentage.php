<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

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
        return sprintf(gettext('%d Percent'), $value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
