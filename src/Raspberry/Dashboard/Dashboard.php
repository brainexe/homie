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
     * @param integer $user_id
     * @return array[]
     */
    public function getDashboard($user_id)
    {
        return $this->dashboardGateway->getDashboard($user_id);
    }

    /**
     * @return WidgetInterface[]
     */
    public function getAvailableWidgets()
    {
        return $this->widgetFactory->getAvailableWidgets();
    }

    /**
     * @param integer $user_id
     * @param string $type
     * @param array $payload
     */
    public function addWidget($user_id, $type, array $payload)
    {
        $widget = $this->widgetFactory->getWidget($type);
        $widget->validate($payload);

        $payload['type'] = $type;

        $this->dashboardGateway->addWidget($user_id, $payload);
    }

    /**
     * @param integer $user_id
     * @param integer $widget_id
     */
    public function deleteWidget($user_id, $widget_id)
    {
        $this->dashboardGateway->deleteWidget($user_id, $widget_id);
    }
}
