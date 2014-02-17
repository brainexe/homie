<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\ControllerInterface;
use Silex\Application;
use Matze\Annotations\Annotations as DI;

/**
 * @DI\Controller
 */
class IndexController implements ControllerInterface {

	/**
	 * @return string
	 */
	public function getPath() {
		return '/';
	}

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function (Application $app) {
			return $app['twig']->render('index.html.twig');
		});

		return $controllers;
	}
}