<?php

namespace Raspberry\Controller;

use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\AddFlashTrait;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\RadioJob;
use Raspberry\Radio\Radios;
use Raspberry\Radio\VO\RadioVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class RadioController implements ControllerInterface {

	use AddFlashTrait;
	use EventDispatcherTrait;

	/**
	 * @var Radios;
	 */
	private $radios;

	/**
	 * @var RadioJob
	 */
	private $radioJob;

	/**
	 * @Inject({"@Radios", "@RadioJob"})
	 * @param Radios $radios
	 * @param RadioJob $radio_job
	 */
	public function __construct(Radios $radios, RadioJob $radio_job) {
		$this->radios    = $radios;
		$this->radioJob = $radio_job;
	}

	/**
	 * @return array
	 * @Route("/radio/", name="radio.index")
	 */
	public function index() {
		$radios_formatted = $this->radios->getRadios();
		$jobs = $this->radioJob->getJobs();

		return [
			'radios'     => $radios_formatted,
			'radio_jobs' => $jobs,
			'pins'       => Radios::$radio_pins,
		];
	}

	/**
	 * @param Request $request
	 * @param integer $radio_id
	 * @param integer $status
	 * @return JsonResponse
	 * @Route("/radio/status/{radio_id}/{status}/", name="radio.set_status", methods="POST")
	 */
	public function setStatus(Request $request, $radio_id, $status) {
		$radio_vo = $this->radios->getRadio($radio_id);

		$event = new RadioChangeEvent($radio_vo, $status);
		$this->dispatchInBackground($event);

		$response = new JsonResponse(true);
		$this->_addFlash($response, self::ALERT_SUCCESS, _('Set Radio'));

		return $response;
	}

	/**
	 * @param Request $request
	 * @return RadioVO
	 * @Route("/radio/add/", methods="POST")
	 */
	public function addRadio(Request $request) {
		$name        = $request->request->get('name');
		$description = $request->request->get('description');
		$code        = $request->request->get('code');
		$pin_raw     = $request->request->get('pin');

		$pin = $this->radios->getRadioPin($pin_raw);

		$radio_vo = new RadioVO();
		$radio_vo->name        = $name;
		$radio_vo->description = $description;
		$radio_vo->code        = $code;
		$radio_vo->pin         = $pin;

		$this->radios->addRadio($radio_vo);

		return $radio_vo;
	}

	/**
	 * @param Request $request
	 * @param integer $radio_id
	 * @return boolean
	 * @Route("/radio/delete/{radio_id}/", name="radio.delete", methods="POST")
	 */
	public function deleteRadio(Request $request, $radio_id) {
		$this->radios->deleteRadio($radio_id);

		return true;
	}

	/**
	 * @param Request $request
	 * @return boolean
	 * @Route("/radio/edit/", name="radio.edit", methods="POST")
	 */
	public function editRadio(Request $request) {
		$radio_id = $request->request->getInt('radio_id');

		// TODO

		return true;
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/radio/job/add/", name="radiojob.add", methods="POST")
	 */
	public function addRadioJob(Request $request) {
		$radio_id    = $request->request->getInt('radio_id');
		$status      = $request->request->getInt('status');
		$time_string = $request->request->get('time');

		$radio_vo = $this->radios->getRadio($radio_id);

		$this->radioJob->addRadioJob($radio_vo, $time_string, $status);

		$response = new JsonResponse(true);
		$this->_addFlash($response, self::ALERT_SUCCESS, _('The job was sored successfully'));

		return $response;
	}

	/**
	 * @param Request $request
	 * @param string $job_id
	 * @return boolean
	 * @Route("/radio/job/delete/{job_id}/", methods="POST")
	 */
	public function deleteRadioJob(Request $request, $job_id) {
		$this->radioJob->deleteJob($job_id);

		return true;
	}
}
