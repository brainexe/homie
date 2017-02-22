<?php

namespace Homie\Dashboard;

use BrainExe\Core\Annotations\Service;
use InvalidArgumentException;

/**
 * @Service("WidgetFactory")
 */
class WidgetFactory
{

    /**
     * @var WidgetInterface[]
     */
    private $widgets;

    /**
     * @param WidgetInterface[] $widgets
     */
    public function __construct(array $widgets)
    {
        $this->widgets = $widgets;
    }

    /**
     * @param string $widgetType
     * @param WidgetInterface $widget
     */
    public function addWidget(string $widgetType, WidgetInterface $widget)
    {
        $this->widgets[$widgetType] = $widget;
    }

    /**
     * @param string $widgetType
     * @return WidgetInterface
     * @throws InvalidArgumentException
     */
    public function getWidget(string $widgetType) : WidgetInterface
    {
        if (empty($this->widgets[$widgetType])) {
            throw new InvalidArgumentException(sprintf('Invalid widget: %s', $widgetType));
        }

        return $this->widgets[$widgetType];
    }

    /**
     * @return WidgetInterface[]
     */
    public function getAvailableWidgets() : array
    {
        return $this->widgets;
    }
}
