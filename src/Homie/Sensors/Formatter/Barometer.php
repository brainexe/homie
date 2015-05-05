<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;
use Homie\Sensors\Definition;

/**
 * @SensorFormatter("Formatter.Barometer")
 */
class Barometer implements Formatter
{

    const TYPE = 'barometer';

    /**
     * @todo
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
        return $this->formatValue($value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
