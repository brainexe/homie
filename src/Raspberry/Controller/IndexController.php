<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\TwigTrait;
use Raspberry\Dashboard\Dashboard;

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
	 */
	public function getRoutes() {
		return [
			'index.index' => [
				'pattern' => '/',
				'defaults' => ['_controller' => 'Index::index']
			]
		];
	}

	/**
	 * @Route('/')
	 */
	public function index() {
		$user_id = 0; //TODO

		$widgets = $this->_dashboard->getWidgets($user_id);

		return $this->render('index.html.twig', [
			'widgets' => $widgets
		]);
	}

}
