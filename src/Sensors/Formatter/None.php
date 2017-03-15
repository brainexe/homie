<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter
 */
class None extends Formatter
{
    const TYPE = 'none';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value) : string
    {
        return (string)$value;
    }
}
