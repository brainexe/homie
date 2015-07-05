<?php

namespace Homie\Dashboard\Widgets;

class WidgetMetadataVo
{
    const MULTI_SELECT  = 'multi_select';
    const SINGLE_SELECT = 'single_select';
    const TEXT          = 'text';

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

    public function addTitle()
    {
        $new = ['title' => [
            'name'    => gettext('Title'),
            'type'    => self::TEXT,
            'default' => $this->name
        ]];
        $this->parameters = $new + $this->parameters;
    }
}
