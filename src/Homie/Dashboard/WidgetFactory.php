<?php

namespace Homie\Dashboard;

use BrainExe\Annotations\Annotations\Service;
use InvalidArgumentException;

/**
 * @Service(public=false)
 */
class WidgetFactory
{

    /**
     * @var WidgetInterface[]
     */
    private $widgets;

    /**
     * @param string $widgetId
     * @param WidgetInterface $widget
     */
    public function addWidget($widgetId, WidgetInterface $widget)
    {
        $this->widgets[$widgetId] = $widget;
    }

    /**
     * @param WidgetInterface[] $widgets
     */
    public function setWidgets(array $widgets)
    {
        $this->widgets = $widgets;
    }

    /**
     * @param string $widgetId
     * @return WidgetInterface
     * @throws InvalidArgumentException
     */
    public function getWidget($widgetId)
    {
        if (empty($this->widgets[$widgetId])) {
            throw new InvalidArgumentException(sprintf('Invalid widget: %s', $widgetId));
        }

        return $this->widgets[$widgetId];
    }

    /**
     * @return WidgetInterface[]
     */
    public function getAvailableWidgets()
    {
        return $this->widgets;
    }
}
