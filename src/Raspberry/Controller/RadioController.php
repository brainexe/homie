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
	 * @Route("/radio/", name="radio.index")
	 */
	public function index() {
		$radios_formatted = $this->_service_radios->getRadios();

		return $this->render('radio.html.twig', [
			'radios' => $radios_formatted,
			'radio_jobs' => $this->_radio_job->getJobs(),
			'pins' => Radios::$radio_pins,
		]);
	}

	/**
	 * @param integer $radio_id
	 * @param integer $status
	 * @return RedirectResponse
	 * @Route("/radio/status/{radio_id}/{status}/")
	 */
	public function setStatus($radio_id, $status) {
		$radio = $this->_service_radios->getRadio($radio_id);

		$event = new MessageQueueEvent('RadioController', 'setStatus', [$radio['code'], $radio['pin'], (bool)$status]);
		$this->getEventDispatcher()->dispatch(MessageQueueEvent::NAME, $event);

		return new RedirectResponse('/radio/');
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/radio/add/", methods="POST")
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
 	 * @Route("/radio/delete/{radio_id}/", name="radio.delete")
	 */
	public function deleteRadio($radio_id) {
		$this->_service_radios->deleteRadio($radio_id);

		return new RedirectResponse('/radio/');
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/radio/job/add/", name="radiojob.add", methods="POST")
	 */
	public function addRadioJob(Request $request) {
		$radio_id = $request->request->getInt('radio_id');
		$status = $request->request->getInt('status');
		$time_string = $request->request->get('time');

		$this->_radio_job->addRadioJob($radio_id, $time_string, $status);

		return new RedirectResponse('/radio/');
	}

	/**
	 * @param integer $job_id
	 * @return RedirectResponse
	 * @Route("/radio/job/delete/{job_id}/", name="radiojob.delete")
	 */
	public function deleteRadioJob($job_id) {
		$this->_radio_job->deleteJob($job_id);

		return new RedirectResponse('/radio/');
	}
}
