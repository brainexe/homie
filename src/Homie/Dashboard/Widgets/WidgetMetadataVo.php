<?php

namespace Homie\Dashboard\Widgets;

class WidgetMetadataVo
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string[]
     */
    public $parameters = [];

    /**
     * @var string
     */
    public $widgetId;

    /**
     * @param string $widgetId
     * @param string $name
     * @param string[] $parameters
     */
    public function __construct($widgetId, $name, array $parameters = [])
    {
        $this->widgetId   = $widgetId;
        $this->name       = $name;
        $this->parameters = $parameters;
    }
}
