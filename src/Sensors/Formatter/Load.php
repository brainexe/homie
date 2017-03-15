<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter
 */
class Load extends Formatter
{
    const TYPE = 'load';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value) : string
    {
        return sprintf('%0.1f', $value);
    }
}
