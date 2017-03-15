<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter
 */
class Bytes extends Number
{

    const TYPE = 'bytes';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value) : string
    {
        return parent::formatValue($value) . 'B';
    }
}
