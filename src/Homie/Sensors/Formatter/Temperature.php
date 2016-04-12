<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter("Formatter.Temperature")
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

    /**
     * {@inheritdoc}
     */
    public function getEspeakText($value) : string
    {
        return str_replace('.', ',', sprintf(gettext('%0.1f Degree'), $value));
    }
}
