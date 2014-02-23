<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Gpio\GpioManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class GpioController extends AbstractController {

	/**
	 * @var GpioManager;
	 */
	private $_service_gpio_manager;

	/**
	 * @Inject("@GpioManager")
	 */
	public function __construct(GpioManager $service_gpio_manager) {
		$this->_service_gpio_manager = $service_gpio_manager;
	}

	public function index() {
		$pins = $this->_service_gpio_manager->getPins();

		return $this->render('gpio.html.twig', ['pins' => $pins]);
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @param $status
	 * @param $value
	 * @return RedirectResponse
	 */
	public function setStatus(Request $request, $id, $status, $value) {
		$this->_service_gpio_manager->setPin($id, $status, $value);

		return new RedirectResponse('/gpio/');
	}

	/**
	 * @return string
	 */
	public function getRoutes() {
		return [
			'gpio.index' => [
				'pattern' => '/gpio/',
				'defaults' => ['_controller' =>  'Gpio::index']
			],
			'gpio.set' => [
				'pattern' => '/gpio/set/{id}/{status}/{value}/',
				'defaults' => ['_controller' =>  'Gpio::setStats']
			]
		];
	}
}