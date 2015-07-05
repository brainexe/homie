<?php

namespace Homie\Dashboard;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("DashboardController")
 */
class Controller
{

    /**
     * @var Dashboard
     */
    private $dashboard;

    /**
     * @Inject("@Dashboard")
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
    public function metadata()
    {
        $widgets    = $this->dashboard->getAvailableWidgets();

        return [
            'widgets' => $widgets
        ];
    }
    /**
     * @return array
     * @Route("/dashboard/", name="dashboard.index", methods="GET")
     */
    public function dashboard()
    {
        $dashboards = $this->dashboard->getDashboards();

        return [
            'dashboards' => $dashboards,
        ];
    }

    /**
     * @param Request $request
     * @return DashboardVo
     * @Route("/dashboard/", methods="POST")
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
    public function updateDashboard(Request $request, $dashboardId)
    {
        $payload = (array)$request->request->all();

        return $this->dashboard->updateDashboard($dashboardId, $payload);
    }

    /**
     * @param Request $request
     * @return bool
     * @param int $dashboardId
     * @param int $widgetId
     * @Route("/dashboard/widget/{dashboardId}/{widgetId}/", name="dashboard.widget.update", methods="PUT")
     */
    public function updateWidget(Request $request, $dashboardId, $widgetId)
    {
        $payload = $request->request->all();

        $this->dashboard->updateWidget($dashboardId, $widgetId, $payload);

        return true;
    }

    /**
     * @param Request $request
     * @param int $dashboardId
     * @param int $widgetId
     * @Route("/dashboard/{dashboardId}/{widgetId}/", methods="DELETE", name="dashboard.widget.delete")
     * @return DashboardVo
     */
    public function deleteWidget(Request $request, $dashboardId, $widgetId)
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
    public function deleteDashboard(Request $request, $dashboardId)
    {
        unset($request);

        $this->dashboard->delete($dashboardId);

        return true;
    }
}
