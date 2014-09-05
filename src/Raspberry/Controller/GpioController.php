<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Raspberry\Gpio\GpioManager;
use Symfony\Component\HttpFoundation\JsonResponse;
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
	 * @param GpioManager $service_gpio_manager
	 */
	public function __construct(GpioManager $service_gpio_manager) {
		$this->_service_gpio_manager = $service_gpio_manager;
	}

	/**
	 * @Route("/gpio/", name="gpio.index");
	 * @return JsonResponse
	 */
	public function index() {
		$pins = $this->_service_gpio_manager->getPins();

		return new JsonResponse([
			'pins' => $pins->getAll()
		]);
	}

	/**
	 * @param Request $request
	 * @param integer $id
	 * @param string $status
	 * @param integer $value
	 * @return JsonResponse
	 * @Route("/gpio/set/{id}/{status}/{value}/", name="gpio.set", methods="POST")
	 */
	public function setStatus(Request $request, $id, $status, $value) {
		$pin = $this->_service_gpio_manager->setPin($id, $status, $value);

		return new JsonResponse($pin);
	}

}