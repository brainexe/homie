<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;
use Homie\Sensors\Definition;

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
        return str_replace('.', ',', sprintf(gettext('%0.1f Degree'), $value));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
