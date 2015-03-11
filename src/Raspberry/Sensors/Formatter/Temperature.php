<?php

namespace Raspberry\Sensors\Formatter;

use Raspberry\Sensors\CompilerPass\Annotation\SensorFormatter;
use Raspberry\Sensors\Definition;

/**
 * @SensorFormatter("Formatter.Temperature")
 */
class Temperature implements Formatter
{

    const TYPE = 'temperature';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return sprintf('%0.1f°', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEspeakText($value)
    {
        return str_replace('.', ',', sprintf(_('%0.1f Degree'), $value));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
