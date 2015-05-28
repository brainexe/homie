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
     * @Route("/dashboard/", name="dashboard.index", methods="GET")
     */
    public function index()
    {
        $dashboards = $this->dashboard->getDashboards();
        $widgets    = $this->dashboard->getAvailableWidgets();

        return [
            'dashboards' => $dashboards,
            'widgets'   => $widgets
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
     * @Route("/dashboard/{dashboardId}/", methods="PUT", name="dashboard.update")
     * @return array
     */
    public function updateDashboard(Request $request, $dashboardId)
    {
        $payload = (array)$request->request->get('payload');

        return $this->dashboard->updateDashboard($dashboardId, $payload);
    }

    /**
     * @param Request $request
     * @return array
     *
     * @Route("/dashboard/widget/", methods="POST", name="dashboard.widget.update", methods="PUT")
     */
    public function updateWidget(Request $request)
    {
        $dashboardId = $request->request->getAlnum('dashboardId');
        $widgetId    = $request->request->get('widget_id');
        $payload     = (array)$request->request->get('payload');

        $this->dashboard->updateWidget($dashboardId, $widgetId, $payload);

        return $this->dashboard->getDashboard($dashboardId);
    }

    /**
     * @param Request $request
     * @param int $dashboardId
     * @param int $widgetId
     * @Route("/dashboard/{dashboardId}/{widgetId}/", methods="DELETE", name="dashboard.widget.delete")
     * @return array
     */
    public function deleteWidget(Request $request, $dashboardId, $widgetId)
    {
        unset($request);

        $this->dashboard->deleteWidget($dashboardId, $widgetId);

        $dashboard = $this->dashboard->getDashboard($dashboardId);

        return $dashboard;
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
