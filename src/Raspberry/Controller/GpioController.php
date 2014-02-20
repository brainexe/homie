<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\ControllerInterface;
use Raspberry\Gpio\GpioManager;
use Silex\Application;
use Matze\Annotations\Annotations as DI;
use Matze\Core\Annotations as CoreDI;

/**
 * @CoreDI\Controller
 */
class GpioController implements ControllerInterface {

	/**
	 * @var GpioManager;
	 */
	private $_service_gpio_manager;

	/**
	 * @DI\Inject("@GpioManager")
	 */
	public function __construct(GpioManager $service_gpio_manager) {
		$this->_service_gpio_manager = $service_gpio_manager;
	}

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app)  {
			$pins = $this->_service_gpio_manager->getPins();

			return $app['twig']->render('gpio.html.twig', ['pins' => $pins ]);
		});

		$controllers->get('/set/{id}/{status}/{value}/', function($id, $status, $value, Application $app) {
			$this->_service_gpio_manager->setPin($id, $status, $value);

			return $app->redirect('/gpio/');
		});

		return $controllers;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return '/gpio/';
	}
}