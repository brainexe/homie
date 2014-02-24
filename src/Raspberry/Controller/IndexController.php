<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\TranslatorTrait;
use Matze\Core\Traits\TwigTrait;
use Raspberry\Dashboard\Dashboard;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @todo set locale
 * @Controller
 */
class IndexController extends AbstractController {

	use TranslatorTrait;

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
	 */
	public function getRoutes() {
		return [
			'index.index' => [
				'pattern' => '/',
				'defaults' => ['_controller' => 'Index::index']
			],
			'index.addWidget' => [
				'pattern' => '/dashboard/add/',
				'defaults' => ['_controller' => 'Index::addWidget']
			]
		];
	}

	public function index() {
		$user_id = 0; //TODO

		$dashboard = $this->_dashboard->getDashboard($user_id);
		$widgets = $this->_dashboard->getAvailableWidgets();

		return $this->render('index.html.twig', [
			'dashboard' => $dashboard,
			'widgets' => $widgets
		]);
	}

	public function addWidget(Request $request) {
		$type = $request->request->get('type');
		$payload = json_decode($request->request->get('payload'), true);
		$user_id = 0;

		$this->_dashboard->addWidget($user_id, $type, $payload);

		return new RedirectResponse('/');
	}

}
