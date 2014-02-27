<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Radio\RadioGateway;
use Raspberry\Radio\RadioJob;
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
	 * @var RadioJob
	 */
	private $_radio_job;

	/**
	 * @Inject({"@Radios", "@RadioGateway", "@RadioJob"})
	 */
	public function __construct(Radios $radios, RadioGateway $radio_gateway, RadioJob $radio_job) {
		$this->_service_radios = $radios;
		$this->_service_radio_gateway = $radio_gateway;
		$this->_radio_job = $radio_job;
	}

	/**
	 * @return string
	 */
	public function getRoutes() {
		return [
			'radio.index' => [
				'pattern' => '/radio/',
				'defaults' => ['_controller' => 'Radio::index']
			],
			'radio.set' => [
				'pattern' => '/radio/status/{radio_id}/{status}/',
				'defaults' => ['_controller' => 'Radio::setStatus']
			],
			'radio.add' => [
				'pattern' => '/radio/add/',
				'defaults' => ['_controller' => 'Radio::addRadio']
			],
			'radio.delete' => [
				'pattern' => '/radio/delete/{radio_id}/',
				'defaults' => ['_controller' => 'Radio::deleteRadio']
			],
			'radiojob.add' => [
				'pattern' => '/radio/job/add/',
				'defaults' => ['_controller' => 'Radio::addRadioJob']
			]
		];
	}

	public function index() {
		$radios_formatted = $this->_service_radios->getRadios();

		return $this->render('radio.html.twig', [
			'radios' => $radios_formatted,
			'pins' => Radios::$radio_pins,
		]);
	}

	/**
	 * @param integer $radio_id
	 * @param integer $status
	 * @return RedirectResponse
	 */
	public function setStatus($radio_id, $status) {
		$radio = $this->_service_radios->getRadio($radio_id);

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

		$pin = $this->_service_radios->getRadioPin($pin);

		$this->_service_radio_gateway->addRadio($name, $description, $code, $pin);

		return new RedirectResponse('/radio/');
	}

	/**
	 * @param integer $radio_id
	 * @return RedirectResponse
	 */
	public function deleteRadio($radio_id) {
		$this->_service_radios->deleteRadio($radio_id);

		return new RedirectResponse('/radio/');
	}

	/**
	 * {@inheritdoc}
	 */
	public function addRadioJob(Request $request) {
		$radio_id = $request->request->getInt('radio_id');
		$status = $request->request->getInt('status');
		$eta = $request->request->getInt('eta');

		$this->_radio_job->addRadioJob($radio_id, time() + $eta, $status);

		return new RedirectResponse('/radio/');
	}

}
