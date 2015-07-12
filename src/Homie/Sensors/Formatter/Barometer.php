<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter("Formatter.Barometer")
 */
class Barometer implements Formatter
{

    const TYPE = 'barometer';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return $value . 'hPa';
    }

    /**
     * {@inheritdoc}
     */
    public function getEspeakText($value)
    {
        return $this->formatValue($value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
