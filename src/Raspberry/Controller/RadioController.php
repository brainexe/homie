<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Radio\Radios;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Controller
 */
class RadioController extends AbstractController {

	use EventDispatcherTrait;

	/**
	 * @var Radios;
	 */
	private $_service_radios;

	/**
	 * @Inject("@Radios")
	 */
	public function __construct(Radios $radios) {
		$this->_service_radios = $radios;
	}

	/**
	 * @return string
	 */
	public function getRoutes() {
		return [
			'radio.index' => [
				'pattern' => '/radio/',
				'defaults' =>  ['_controller' =>  'Radio::index']
			],
			'radio.set' => [
				'pattern' => '/radio/{id}/{status}/',
				'defaults' =>  ['_controller' =>  'Radio::setStatus']
			]
		];
	}

	public function index() {
		$radios_formatted = $this->_service_radios->getRadios();

		return $this->render('radio.html.twig', ['radios' => $radios_formatted]);
	}

	public function setStatus() {
		$radio = $this->_service_radios->getRadios()[$id];

		$event = new MessageQueueEvent('RadioController', 'setStatus', [$radio['code'], $radio['pin'], $status]);
		$this->getEventDispatcher()->dispatch(MessageQueueEvent::NAME, $event);

		return new RedirectResponse('/radio/');
	}

}