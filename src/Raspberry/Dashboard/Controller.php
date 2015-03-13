<?php

namespace Raspberry\Dashboard;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Controller\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("DashboardController")
 */
class Controller implements ControllerInterface
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
     * @Route("/dashboard/", name="dashboard.index")
     */
    public function index()
    {
        $dashboards = $this->dashboard->getDashboards();
        $widgets   = $this->dashboard->getAvailableWidgets();

        return [
            'dashboards' => $dashboards,
            'widgets'   => $widgets
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/dashboard/add/", methods="POST")
     */
    public function addWidget(Request $request)
    {
        $type        = $request->request->get('type');
        $payload     = (array)$request->request->get('payload');
        $dashboardId = $request->request->get('dashboard_id');

        $this->dashboard->addWidget($dashboardId, $type, $payload);

        return $this->dashboard->getDashboard($dashboardId);
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/dashboard/delete/", methods="POST")
     */
    public function deleteWidget(Request $request)
    {
        $widgetId    = $request->request->getInt('widget_id');
        $dashboardId = $request->request->getInt('dashboard_id');

        $this->dashboard->deleteWidget($dashboardId, $widgetId);

        $dashboard = $this->dashboard->getDashboard($dashboardId);

        return $dashboard;
    }
}
