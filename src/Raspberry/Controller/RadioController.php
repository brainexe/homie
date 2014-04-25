<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\RadioJob;
use Raspberry\Radio\Radios;
use Raspberry\Radio\VO\RadioVO;
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
	 * @var RadioJob
	 */
	private $_radio_job;

	/**
	 * @Inject({"@Radios", "@RadioJob"})
	 */
	public function __construct(Radios $radios, RadioJob $radio_job) {
		$this->_service_radios = $radios;
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
	 * @param Request $request
	 * @param integer $radio_id
	 * @param integer $status
	 * @return RedirectResponse
	 * @Route("/radio/status/{radio_id}/{status}/", csrf=true)
	 */
	public function setStatus(Request $request, $radio_id, $status) {
		$radio_vo = $this->_service_radios->getRadio($radio_id);

		$this->_addFlash($request, self::ALERT_SUCCESS, _('Set Radio'));

		$event = new RadioChangeEvent($radio_vo, $status);
		$this->dispatchInBackground($event);

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

		$radio_vo = new RadioVO();
		$radio_vo->name = $name;
		$radio_vo->description = $description;
		$radio_vo->code = $code;
		$radio_vo->pin = $pin;

		$this->_service_radios->addRadio($radio_vo);

		return new RedirectResponse('/radio/');
	}

	/**
	 * @param Request $request
	 * @param integer $radio_id
	 * @return RedirectResponse
	 * @Route("/radio/delete/{radio_id}/", name="radio.delete")
	 */
	public function deleteRadio(Request $request, $radio_id) {
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

		$radio_vo = $this->_service_radios->getRadio($radio_id);

		$this->_radio_job->addRadioJob($radio_vo, $time_string, $status);

		$this->_addFlash($request, self::ALERT_SUCCESS, _('The job was sored successfully'));

		return new RedirectResponse('/radio/');
	}

	/**
	 * @param Request $request
	 * @param string $job_id
	 * @return RedirectResponse
	 * @Route("/radio/job/delete/{job_id}/", name="radiojob.delete")
	 */
	public function deleteRadioJob(Request $request, $job_id) {
		$this->_radio_job->deleteJob($job_id);

		return new RedirectResponse('/radio/');
	}
}
