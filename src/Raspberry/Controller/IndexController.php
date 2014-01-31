<?php

namespace Raspberry\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

class IndexController implements ControllerProviderInterface {

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function (Application $app) {
			return $app['twig']->render('index.html.twig');
		});

		return $controllers;
	}
}