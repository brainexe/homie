<?php

namespace Homie\Dashboard;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;

/**
 * @Service(public=false)
 */
class Dashboard
{

    use IdGeneratorTrait;

    /**
     * @var WidgetFactory
     */
    private $widgets;

    /**
     * @var DashboardGateway
     */
    private $gateway;

    /**
     * @Inject({"@DashboardGateway", "@WidgetFactory"})
     * @param DashboardGateway $gateway
     * @param WidgetFactory    $widgetFactory
     */
    public function __construct(
        DashboardGateway $gateway,
        WidgetFactory $widgetFactory
    ) {
        $this->widgets = $widgetFactory;
        $this->gateway = $gateway;
    }

    /**
     * @param integer $dashboardId
     * @return DashboardVo
     */
    public function getDashboard($dashboardId)
    {
        return $this->gateway->getDashboard($dashboardId);
    }

    /**
     * @return WidgetInterface[]
     */
    public function getAvailableWidgets()
    {
        return $this->widgets->getAvailableWidgets();
    }

    /**
     * @param integer $dashboardId
     * @param string $type
     * @param array $payload
     * @return DashboardVo
     */
    public function addWidget($dashboardId, $type, array $payload)
    {
        if (!$dashboardId) {
            $dashboardId = $this->generateUniqueId();
            $this->gateway->addDashboard($dashboardId, [
                'name' => gettext('Dashboard') . ' - ' . $dashboardId
            ]);
        }

        $widget = $this->widgets->getWidget($type);
        $widget->validate($payload);

        $payload['type'] = $type;

        $this->gateway->addWidget($dashboardId, $payload);

        return $this->getDashboard($dashboardId);
    }

    /**
     * @param int $dashboardId
     * @param int $widgetId
     * @param array $payload
     */
    public function updateWidget($dashboardId, $widgetId, array $payload)
    {
        $this->gateway->updateWidget($dashboardId, $widgetId, $payload);
    }

    /**
     * @param integer $dashboardId
     * @param integer $widgetId
     */
    public function deleteWidget($dashboardId, $widgetId)
    {
        $this->gateway->deleteWidget($dashboardId, $widgetId);
    }

    /**
     * @return DashboardVo[]
     */
    public function getDashboards()
    {
        return $this->gateway->getDashboards();
    }

    /**
     * @param int $dashboardId
     * @param array $payload
     * @return DashboardVo
     */
    public function updateDashboard($dashboardId, array $payload)
    {
        $this->gateway->updateMetadata($dashboardId, $payload);

        return $this->getDashboard($dashboardId);
    }

    /**
     * @param int $dashboardId
     */
    public function delete($dashboardId)
    {
        $this->gateway->delete($dashboardId);
    }
}
