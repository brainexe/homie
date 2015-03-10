<?php

namespace Raspberry\Sensors\Formatter;

use Raspberry\Sensors\CompilerPass\Annotation\SensorFormatter;
use Raspberry\Sensors\Definition;

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
