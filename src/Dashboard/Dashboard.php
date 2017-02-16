<?php

namespace Homie\Dashboard;


use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use Generator;

/**
 * @Service
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
     * @param int $dashboardId
     * @return DashboardVo
     */
    public function getDashboard(int $dashboardId) : DashboardVo
    {
        return $this->gateway->getDashboard($dashboardId);
    }

    /**
     * @return WidgetInterface[]
     */
    public function getAvailableWidgets() : array
    {
        return $this->widgets->getAvailableWidgets();
    }

    /**
     * @param int $dashboardId
     * @param string $type
     * @param array $payload
     * @return DashboardVo
     */
    public function addWidget($dashboardId, string $type, array $payload) : DashboardVo
    {
        if (!$dashboardId) {
            $dashboardId = $this->generateUniqueId('dashboardid');
            $this->gateway->addDashboard($dashboardId, [
                'name' => gettext('Dashboard') . ' - ' . $dashboardId
            ]);
        }

        $payload['type'] = $type;

        $this->gateway->addWidget($dashboardId, $payload);

        return $this->getDashboard($dashboardId);
    }

    /**
     * @param int $dashboardId
     * @param int $widgetId
     * @param array $payload
     */
    public function updateWidget(int $dashboardId, int $widgetId, array $payload)
    {
        $this->gateway->updateWidget($dashboardId, $widgetId, $payload);
    }

    /**
     * @param int $dashboardId
     * @param int $widgetId
     */
    public function deleteWidget(int $dashboardId, int $widgetId)
    {
        $this->gateway->deleteWidget($dashboardId, $widgetId);
    }

    /**
     * @return Generator|DashboardVo[]
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
    public function updateDashboard(int $dashboardId, array $payload) : DashboardVo
    {
        $this->gateway->updateMetadata($dashboardId, $payload);

        return $this->getDashboard($dashboardId);
    }

    /**
     * @param int $dashboardId
     */
    public function delete(int $dashboardId)
    {
        $this->gateway->delete($dashboardId);
    }
}
