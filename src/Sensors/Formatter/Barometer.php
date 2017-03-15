<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter
 */
class Barometer extends Formatter
{

    const TYPE = 'barometer';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value) : string
    {
        return $value . 'hPa';
    }
}
