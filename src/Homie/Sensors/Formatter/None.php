<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;
use Homie\Sensors\Definition;

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
        return (string)$value;
    }

    /**
     * {@inheritdoc}
     */
    public function getEspeakText($value)
    {
        return (string)$value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
