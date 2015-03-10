<?php

namespace Raspberry\Sensors\Formatter;

use Raspberry\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter("Formatter.Load")
 */
class Load implements Formatter
{
    const TYPE = 'load';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return sprintf('%0.1f', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEspeakText($value)
    {
        return sprintf('%1.1f', $value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
