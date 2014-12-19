<?php

namespace Raspberry\Controller;

use BrainExe\Core\Controller\ControllerInterface;
use Raspberry\Dashboard\Dashboard;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class DashboardController implements ControllerInterface
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
        $user_id = $this->getUserId($request);

        $dashboard = $this->dashboard->getDashboard($user_id);
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
        $payload = $request->request->get('payload');
        $user_id = $this->getUserId($request);

        $this->dashboard->addWidget($user_id, $type, $payload);

        return $this->dashboard->getDashboard($user_id);
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/dashboard/delete/", methods="POST")
     */
    public function deleteWidget(Request $request)
    {
        $widget_id = $request->request->getInt('widget_id');
        $user_id   = $this->getUserId($request);

        $this->dashboard->deleteWidget($user_id, $widget_id);

        $dashboard = $this->dashboard->getDashboard($user_id);

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
