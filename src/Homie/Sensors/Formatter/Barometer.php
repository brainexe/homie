<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter("Formatter.Barometer")
 */
class Barometer extends Formatter
{

    const TYPE = 'barometer';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return $value . 'hPa';
    }
}
