<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Gpio\GpioManager;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

	/**
	 * @Route("/gpio/", name="gpio.index");
	 * @return string
	 */
	public function index() {
		$pins = $this->_service_gpio_manager->getPins();

		return $this->render('gpio.html.twig', ['pins' => $pins]);
	}

	/**
	 * @param integer $id
	 * @param string $status
	 * @param integer $value
	 * @return RedirectResponse
	 * @Route("/gpio/set/{id}/{status}/{value}/", name="gpio.set")
	 */
	public function setStatus($id, $status, $value) {
		$this->_service_gpio_manager->setPin($id, $status, $value);

		return new RedirectResponse('/gpio/');
	}

}