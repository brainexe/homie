<?php

namespace Raspberry\Dashboard;

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
     * @param WidgetFactory $widgetFactory
     */
    public function __construct(DashboardGateway $dashboardGateway, WidgetFactory $widgetFactory)
    {
        $this->widgetFactory    = $widgetFactory;
        $this->dashboardGateway = $dashboardGateway;
    }

    /**
     * @param integer $userId
     * @return array[]
     */
    public function getDashboard($userId)
    {
        return $this->dashboardGateway->getDashboard($userId);
    }

    /**
     * @return WidgetInterface[]
     */
    public function getAvailableWidgets()
    {
        return $this->widgetFactory->getAvailableWidgets();
    }

    /**
     * @param integer $userId
     * @param string $type
     * @param array $payload
     */
    public function addWidget($userId, $type, array $payload)
    {
        $widget = $this->widgetFactory->getWidget($type);
        $widget->validate($payload);

        $payload['type'] = $type;

        $this->dashboardGateway->addWidget($userId, $payload);
    }

    /**
     * @param integer $userId
     * @param integer $widgetId
     */
    public function deleteWidget($userId, $widgetId)
    {
        $this->dashboardGateway->deleteWidget($userId, $widgetId);
    }
}
