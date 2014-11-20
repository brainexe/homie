<?php

namespace Raspberry\Controller;

use BrainExe\Core\Controller\ControllerInterface;
use Raspberry\Dashboard\Dashboard;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class DashboardController implements ControllerInterface {

	/**
	 * @var Dashboard
	 */
	private $_dashboard;

	/**
	 * @Inject("@Dashboard")
	 * @param Dashboard $dashboard
	 */
	public function __construct(Dashboard $dashboard) {
		$this->_dashboard = $dashboard;
	}

	/**
	 * @param Request $request
	 * @return array
	 * @Route("/dashboard/", name="dashboard.index")
	 */
	public function index(Request $request) {
		$user_id = $this->_getUserId($request);

		$dashboard = $this->_dashboard->getDashboard($user_id);
		$widgets   = $this->_dashboard->getAvailableWidgets();

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
	public function addWidget(Request $request) {
		$type    = $request->request->get('type');
		$payload = (array)json_decode($request->request->get('payload'), true);
		$user_id = $this->_getUserId($request);

		$this->_dashboard->addWidget($user_id, $type, $payload);

		$dashboard = $this->_dashboard->getDashboard($user_id);

		return $dashboard;
	}

	/**
	 * @param Request $request
	 * @return array
	 * @Route("/dashboard/delete/", methods="POST")
	 */
	public function deleteWidget(Request $request) {
		$widget_id = $request->request->getInt('widget_id');
		$user_id   = $this->_getUserId($request);

		$this->_dashboard->deleteWidget($user_id, $widget_id);

		$dashboard = $this->_dashboard->getDashboard($user_id);

		return $dashboard;
	}

	/**
	 * @param Request $request
	 * @return integer
	 */
	private function _getUserId(Request $request) {
		return 0; //TODO
	}

}
