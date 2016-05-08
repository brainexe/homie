<?php

namespace Homie\Dashboard\Widgets;

class WidgetMetadataVo
{
    const MULTI_SELECT   = 'multi_select';
    const SINGLE_SELECT  = 'single_select';
    const TEXT           = 'text';
    const NUMBER         = 'number';
    const TEXT_AREA      = 'text_area';
    const KEY_VALUE_LIST = 'key_value_list';
    const KEY_BOOLEAN    = 'boolean';

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
     * @var int
     */
    public $height;

    /**
     * @param string $widgetId
     * @param array[] $parameters
     */
    public function __construct(string $widgetId, array $parameters = [])
    {
        $this->widgetId   = $widgetId;
        $this->parameters = $parameters;

        $this->setSize(4, 2);
    }

    /**
     * @param int $width
     * @param int $height
     * @return self
     */
    public function setSize(int $width, int $height)
    {
        $this->width  = $width;
        $this->height = $height;

        $this->parameters['width'] = [
            'name'    => gettext('Width'),
            'type'    => self::NUMBER,
            'min'     => 1,
            'max'     => 12,
            'default' => $width
        ];
        $this->parameters['height'] = [
            'name'    => gettext('Height'),
            'type'    => self::NUMBER,
            'min'     => 1,
            'max'     => 12,
            'default' => $height
        ];

        return $this;
    }

    /**
     * @return self
     */
    public function addTitle()
    {
        $new = ['title' => [
            'name'    => gettext('Title'),
            'type'    => self::TEXT,
        ]];
        $this->parameters = $new + $this->parameters;

        return $this;
    }
}
