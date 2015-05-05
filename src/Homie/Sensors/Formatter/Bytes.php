<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;
use Homie\Sensors\Definition;

/**
 * @SensorFormatter("Formatter.Bytes")
 */
class Bytes implements Formatter
{

    const TYPE = 'bytes';


    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return sprintf('%dMB', $value / 1000000);
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
