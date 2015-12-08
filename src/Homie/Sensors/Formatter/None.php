<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter("Formatter.None")
 */
class None extends Formatter
{
    const TYPE = 'none';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return (string)$value;
    }
}
