<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter("Formatter.Bytes")
 */
class Bytes extends Formatter
{

    const TYPE = 'bytes';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return sprintf('%dMB', $value / 1000000);
    }
}
