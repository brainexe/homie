<?php

namespace Homie\Dashboard;

use BrainExe\Core\Translation\TranslationProvider;
use JsonSerializable;

abstract class AbstractWidget implements WidgetInterface, JsonSerializable, TranslationProvider
{

    const TYPE = 'none';

    const TOKEN_NAME = 'dashboard.widget.%s.name';
    const TOKEN_DESCRIPTION = 'dashboard.widget.%s.description';

    /**
     * @return string
     */
    public function getId()
    {
        return static::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $payload)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $payload)
    {
        return true;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getMetadata();
    }

    /**
     * @return string[]
     */
    public static function getTokens()
    {
        yield sprintf(self::TOKEN_NAME, self::getId());
        yield sprintf(self::TOKEN_DESCRIPTION, self::getId());
    }
}
