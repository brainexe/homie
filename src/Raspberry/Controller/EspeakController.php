<?php

namespace Raspberry\Controller;

use BrainExe\Core\Controller\AbstractController;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use Raspberry\Espeak\Espeak;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class EspeakController extends AbstractController {

	use EventDispatcherTrait;

	/**
	 * @var Espeak
	 */
	private $_espeak;

	/**
	 * @var TimeParser
	 */
	private $_time_parser;

	/**
	 * @Inject({"@Espeak", "@TimeParser"})
	 * @param Espeak $espeak
	 * @param TimeParser $time_parser
	 */
	public function __construct(Espeak $espeak, TimeParser $time_parser) {
		$this->_espeak = $espeak;
		$this->_time_parser = $time_parser;
	}

	/**
	 * @return JsonResponse
	 * @Route("/espeak/", name="espeak.index")
	 */
	public function index() {
		$speakers = $this->_espeak->getSpeakers();

		return new JsonResponse([
			'speakers' => $speakers,
			'jobs' => $this->_espeak->getPendingJobs()
		]);
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/espeak/speak/", methods="POST")
	 */
	public function speak(Request $request) {
		$speaker = $request->request->get('speaker') ?: null;
		$text = $request->request->get('text');
		$volume = $request->request->getInt('volume');
		$speed = $request->request->getInt('speed');
		$delay_raw = $request->request->get('delay');

		$timestamp = $this->_time_parser->parseString($delay_raw);

		$espeak_vo = new EspeakVO($text, $volume, $speed, $speaker);
		$event = new EspeakEvent($espeak_vo);

		$this->dispatchInBackground($event, $timestamp);

		return new JsonResponse($this->_espeak->getPendingJobs());
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/espeak/job/delete/", name="espeak.delete", methods="POST")
	 */
	public function deleteJobJob(Request $request) {
		$job_id = $request->request->get('job_id');

		$this->_espeak->deleteJob($job_id);

		return new JsonResponse(true);
	}
}