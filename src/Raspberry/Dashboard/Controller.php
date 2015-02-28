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
     * @param Request $request
     * @return array
     * @Route("/dashboard/", name="dashboard.index")
     */
    public function index(Request $request)
    {
        $userId = $this->getUserId($request);

        $dashboard = $this->dashboard->getDashboard($userId);
        $widgets   = $this->dashboard->getAvailableWidgets();

        return [
            'dashboard' => $dashboard,
            'widgets' => $widgets
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/dashboard/add/", methods="POST")
     */
    public function addWidget(Request $request)
    {
        $type    = $request->request->get('type');
        $payload = (array)$request->request->get('payload');
        $userId  = $this->getUserId($request);

        $this->dashboard->addWidget($userId, $type, $payload);

        return $this->dashboard->getDashboard($userId);
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/dashboard/delete/", methods="POST")
     */
    public function deleteWidget(Request $request)
    {
        $widgetId = $request->request->getInt('widget_id');
        $userId   = $this->getUserId($request);

        $this->dashboard->deleteWidget($userId, $widgetId);

        $dashboard = $this->dashboard->getDashboard($userId);

        return $dashboard;
    }

    /**
     * @param Request $request
     * @return integer
     */
    private function getUserId(Request $request)
    {
        return $request->attributes->get('user_id');
    }
}
