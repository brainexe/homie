<?php

namespace Homie\Dashboard;


use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Dashboard.Controller", requirements={"dashboardId":"\d+", "widgetId":"\d+"})
 */
class Controller
{
    /**
     * @var Dashboard
     */
    private $dashboard;

    /**
     * @param Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * @return array
     * @Route("/dashboard/metadata/", name="dashboard.metadata", methods="GET")
     */
    public function metadata() : array
    {
        $widgets = $this->dashboard->getAvailableWidgets();

        return [
            'widgets' => $widgets
        ];
    }
    /**
     * @return array
     * @Route("/dashboard/", name="dashboard.index", methods="GET")
     */
    public function dashboard() : array
    {
        $dashboards = $this->dashboard->getDashboards();

        return [
            'dashboards' => iterator_to_array($dashboards),
        ];
    }

    /**
     * @param Request $request
     * @return DashboardVo
     * @Route("/dashboard/", methods="POST", name="dashboard.addWidget")
     */
    public function addWidget(Request $request)
    {
        $type        = $request->request->get('type');
        $payload     = (array)$request->request->get('payload');
        $dashboardId = $request->request->getInt('dashboardId');

        return $this->dashboard->addWidget($dashboardId, $type, $payload);
    }

    /**
     * @param Request $request
     * @param int $dashboardId
     * @return DashboardVo
     * @Route("/dashboard/{dashboardId}/", methods="PUT", name="dashboard.update")
     */
    public function updateDashboard(Request $request, int $dashboardId) : DashboardVo
    {
        $payload = (array)$request->request->all();

        return $this->dashboard->updateDashboard($dashboardId, $payload);
    }

    /**
     * @param Request $request
     * @return DashboardVo
     * @param int $dashboardId
     * @param int $widgetId
     * @Route("/dashboard/widget/{dashboardId}/{widgetId}/", name="dashboard.widget.update", methods="PUT")
     */
    public function updateWidget(Request $request, int $dashboardId, int $widgetId) : DashboardVo
    {
        $payload = $request->request->all();

        $this->dashboard->updateWidget($dashboardId, $widgetId, $payload);

        return $this->dashboard->getDashboard($dashboardId);
    }

    /**
     * @param Request $request
     * @param int $dashboardId
     * @param int $widgetId
     * @Route("/dashboard/{dashboardId}/{widgetId}/", methods="DELETE", name="dashboard.widget.delete")
     * @return DashboardVo
     */
    public function deleteWidget(Request $request, int $dashboardId, int $widgetId) : DashboardVo
    {
        unset($request);

        $this->dashboard->deleteWidget($dashboardId, $widgetId);

        return $this->dashboard->getDashboard($dashboardId);
    }

    /**
     * @param Request $request
     * @param int $dashboardId
     * @Route("/dashboard/{dashboardId}/", methods="DELETE", name="dashboard.delete")
     * @return bool
     */
    public function deleteDashboard(Request $request, int $dashboardId) : bool
    {
        unset($request);

        $this->dashboard->delete($dashboardId);

        return true;
    }
}
