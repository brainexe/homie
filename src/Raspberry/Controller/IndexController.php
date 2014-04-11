<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\TwigTrait;
use Raspberry\Dashboard\Dashboard;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class IndexController extends AbstractController {

	/**
	 * @var Dashboard
	 */
	private $_dashboard;

	/**
	 * @Inject("@Dashboard")
	 */
	public function __construct(Dashboard $dashboard) {
		$this->_dashboard = $dashboard;
	}

	/**
	 * @return string
	 * @Route("/", name="index")
	 */
	public function index() {
//		$user = $request->getSession()->get('user');
		$user_id = 0; //TODO

		$dashboard = $this->_dashboard->getDashboard($user_id);
		$widgets = $this->_dashboard->getAvailableWidgets();

		return $this->render('index.html.twig', [
			'dashboard' => $dashboard,
			'widgets' => $widgets
		]);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/dashboard/add/", methods="POST")
	 */
	public function addWidget(Request $request) {
		$type = $request->request->get('type');
		$payload = (array)json_decode($request->request->get('payload'), true);
		$user_id = 0;

		$this->_dashboard->addWidget($user_id, $type, $payload);

		return new RedirectResponse('/');
	}

}
