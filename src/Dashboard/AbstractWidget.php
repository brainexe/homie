<?php

namespace Homie\Dashboard;

use BrainExe\Core\Translation\TranslationProvider;
use Generator;
use Homie\Dashboard\Widgets\WidgetMetadataVo;
use JsonSerializable;

abstract class AbstractWidget implements WidgetInterface, JsonSerializable, TranslationProvider
{

    const TOKEN_NAME        = 'dashboard.widget.%s.name';
    const TOKEN_DESCRIPTION = 'dashboard.widget.%s.description';

    /**
     * @return string
     */
    public function getId() : string
    {
        return static::TYPE;
    }

    /**
     * @return WidgetMetadataVo
     */
    public function jsonSerialize() : WidgetMetadataVo
    {
        return $this->getMetadata();
    }

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata() : WidgetMetadataVo
    {
        $metadata = new WidgetMetadataVo(
            $this->getId()
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }

    /**
     * @return Generator|string[]
     */
    public static function getTokens()
    {
        yield sprintf(self::TOKEN_NAME, static::TYPE);
        yield sprintf(self::TOKEN_DESCRIPTION, static::TYPE);
    }
}
