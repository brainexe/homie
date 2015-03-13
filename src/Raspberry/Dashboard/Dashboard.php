<?php

namespace Raspberry\Dashboard;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

/**
 * @Service(public=false)
 */
class Dashboard
{

    /**
     * @var WidgetFactory
     */
    private $widgetFactory;

    /**
     * @var DashboardGateway
     */
    private $dashboardGateway;

    /**
     * @Inject({"@DashboardGateway", "@WidgetFactory"})
     * @param DashboardGateway $dashboardGateway
     * @param WidgetFactory    $widgetFactory
     */
    public function __construct(DashboardGateway $dashboardGateway, WidgetFactory $widgetFactory)
    {
        $this->widgetFactory    = $widgetFactory;
        $this->dashboardGateway = $dashboardGateway;
    }

    /**
     * @param integer $dashboardId
     * @return DashboardVo
     */
    public function getDashboard($dashboardId)
    {
        return $this->dashboardGateway->getDashboard($dashboardId);
    }

    /**
     * @return WidgetInterface[]
     */
    public function getAvailableWidgets()
    {
        return $this->widgetFactory->getAvailableWidgets();
    }

    /**
     * @param integer $dashboardId
     * @param string $type
     * @param array $payload
     */
    public function addWidget($dashboardId, $type, array $payload)
    {
        $widget = $this->widgetFactory->getWidget($type);
        $widget->validate($payload);

        $payload['type'] = $type;

        $this->dashboardGateway->addWidget($dashboardId, $payload);
    }

    /**
     * @param integer $dashboardId
     * @param integer $widgetId
     */
    public function deleteWidget($dashboardId, $widgetId)
    {
        $this->dashboardGateway->deleteWidget($dashboardId, $widgetId);
    }

    /**
     * @return DashboardVo[]
     */
    public function getDashboards()
    {
        return $this->dashboardGateway->getDashboards();
    }

    /**
     * @param int $dashboardId
     * @param string $name
     * @return DashboardVo
     */
    public function updateDashboard($dashboardId, $name)
    {
        $this->dashboardGateway->updateDashboard($dashboardId, $name);

        return $this->getDashboard($dashboardId);
    }
}
