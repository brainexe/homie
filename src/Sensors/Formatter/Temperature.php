<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter
 */
class Temperature extends Formatter
{

    const TYPE = 'temperature';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value) : string
    {
        return sprintf('%s°', $value);
    }
}
