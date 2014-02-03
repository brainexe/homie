<?php

namespace Raspberry\Controller;

use Silex\Application;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(name="Controller.IndexController", public=false, tags={{"name" = "controller"}})
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