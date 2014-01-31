<?php

namespace Raspberry\Controller;

use Raspberry\Gpio\GpioManager;
use Silex\Application;
use Silex\ControllerProviderInterface;

class GpioController implements ControllerProviderInterface {

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app)  {
			/** @var GpioManager $gpio_manager */
			$gpio_manager = $app['dic']->get('GpioManager');

			$pins = $gpio_manager->getPins();

			return $app['twig']->render('gpio.html.twig', ['pins' => $pins ]);
		});

		$controllers->get('/set/{id}/{status}/{value}/', function($id, $status, $value, Application $app) {
			/** @var GpioManager $gpio_manager */
			$gpio_manager = $app['dic']->get('GpioManager');

			$gpio_manager->setPin($id, $status, $value);

			return $app->redirect('/gpio/');
		});

		return $controllers;
	}

}