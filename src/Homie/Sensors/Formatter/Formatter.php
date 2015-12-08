<?php

namespace Homie\Sensors\Formatter;

use BrainExe\Core\Translation\TranslationProvider;

abstract class Formatter implements TranslationProvider
{
    const TOKEN_NAME        = 'sensor.formatter.%s.name';
    const TOKEN_DESCRIPTION = 'sensor.formatter.%s.description';
    const TYPE              = 'unknown';

    /**
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * @param double $value
     * @return string
     */
    abstract public function formatValue($value);

    /**
     * @param float $value
     * @return string|null
     */
    public function getEspeakText($value)
    {
        return $this->formatValue($value);
    }

    /**
     * @return string[]
     */
    public static function getTokens()
    {
        return [
            sprintf(self::TOKEN_NAME, static::TYPE),
            sprintf(self::TOKEN_DESCRIPTION, static::TYPE),
        ];
    }
}
