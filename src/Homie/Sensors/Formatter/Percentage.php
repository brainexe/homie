<?php

namespace Homie\Sensors\Formatter;

use BrainExe\Core\Translation\TranslationTrait;
use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter("Formatter.Percentage")
 */
class Percentage extends Formatter
{
    const TYPE = 'percentage';

    use TranslationTrait;

    /**
     * @param double $value
     * @return string
     */
    public function formatValue($value) : string
    {
        return sprintf('%d%%', $value);
    }
}
