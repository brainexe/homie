<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Raspberry\Dashboard\Dashboard;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class DashboardController extends AbstractController {

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
	 * @return JsonResponse
	 * @Route("/dashboard/", name="dashboard.index")
	 */
	public function index() {
//		$user = $request->attributes->get('user');
		$user_id = 0; //TODO

		$dashboard = $this->_dashboard->getDashboard($user_id);
		$widgets = $this->_dashboard->getAvailableWidgets();

		return new JsonResponse([
			'dashboard' => $dashboard,
			'widgets' => $widgets
		]);
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/dashboard/add/", methods="POST")
	 */
	public function addWidget(Request $request) {
		$type = $request->request->get('type');
		$payload = (array)json_decode($request->request->get('payload'), true);
		$user_id = 0;

		$this->_dashboard->addWidget($user_id, $type, $payload);

		return new JsonResponse(true);
	}

}
