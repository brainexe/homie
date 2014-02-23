<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\TwigTrait;

/**
 * @Controller
 */
class IndexController extends AbstractController {

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
		return $this->render('index.html.twig');
	}

}