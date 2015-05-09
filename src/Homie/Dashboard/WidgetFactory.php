<?php

namespace Homie\Dashboard;

use BrainExe\Annotations\Annotations\Service;
use InvalidArgumentException;

/**
 * @Service(public=true)
 */
class WidgetFactory
{

    /**
     * @var WidgetInterface[]
     */
    private $widgets;

    /**
     * @param WidgetInterface $widget
     */
    public function addWidget(WidgetInterface $widget)
    {
        $this->widgets[$widget->getId()] = $widget;
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
        return array_values($this->widgets);
    }
}
