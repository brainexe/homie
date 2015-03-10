<?php

namespace Raspberry\Sensors\Formatter;

use Raspberry\Sensors\CompilerPass\Annotation\SensorFormatter;
use Raspberry\Sensors\Definition;

/**
 * @SensorFormatter("Formatter.None")
 */
class None implements Formatter
{
    const TYPE = 'none';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getEspeakText($value)
    {
        return $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
