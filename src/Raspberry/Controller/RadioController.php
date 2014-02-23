<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Radio\RadioGateway;
use Raspberry\Radio\Radios;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
	 * @var RadioGateway
	 */
	private $_service_radio_gateway;

	/**
	 * @Inject({"@Radios", "@RadioGateway"})
	 */
	public function __construct(Radios $radios, RadioGateway $radio_gateway) {
		$this->_service_radios = $radios;
		$this->_service_radio_gateway = $radio_gateway;
	}

	/**
	 * @return string
	 */
	public function getRoutes() {
		return [
			'radio.index' => [
				'pattern' => '/radio/',
				'defaults' => ['_controller' =>  'Radio::index']
			],
			'radio.set' => [
				'pattern' => '/radio/{id}/{status}/',
				'defaults' => ['_controller' =>  'Radio::setStatus']
			],
			'radio.add' => [
				'pattern' => '/radio/add/',
				'defaults' => ['_controller' =>  'Radio::addRadio']
			]
		];
	}

	public function index() {
		$radios_formatted = $this->_service_radios->getRadios();

		return $this->render('radio.html.twig', ['radios' => $radios_formatted]);
	}

	/**
	 * @param integer $id
	 * @param integer $status
	 * @return RedirectResponse
	 */
	public function setStatus($id, $status) {
		$radio = $this->_service_radios->getRadios()[$id];

		$event = new MessageQueueEvent('RadioController', 'setStatus', [$radio['code'], $radio['pin'], $status]);
		$this->getEventDispatcher()->dispatch(MessageQueueEvent::NAME, $event);

		return new RedirectResponse('/radio/');
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function addRadio(Request $request) {
		$name = $request->request->get('name');
		$description = $request->request->get('description');
		$code = $request->request->get('code');
		$pin = $request->request->get('pin');

		$this->_service_radio_gateway->addRadio($name, $description, $pin, $code);

		return new RedirectResponse('/radio/');
	}

}