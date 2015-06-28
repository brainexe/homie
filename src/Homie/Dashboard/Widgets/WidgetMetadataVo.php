<?php

namespace Homie\Dashboard\Widgets;

class WidgetMetadataVo
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var array[]
     */
    public $parameters = [];

    /**
     * @var string
     */
    public $widgetId;

    /**
     * @var int
     */
    public $width;

    /**
     * @param string $widgetId
     * @param string $name
     * @param string $description
     * @param array[] $parameters
     * @param int $width
     */
    public function __construct($widgetId, $name, $description, array $parameters = [], $width = 6)
    {
        $this->widgetId    = $widgetId;
        $this->name        = $name;
        $this->description = $description;
        $this->parameters  = $parameters;
        $this->width       = $width;
    }
}
