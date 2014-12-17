<?php

namespace Raspberry\Controller;

use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use BrainExe\MessageQueue\MessageQueueJob;
use Raspberry\Espeak\Espeak;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class EspeakController implements ControllerInterface {

	use EventDispatcherTrait;

	/**
	 * @var Espeak
	 */
	private $espeak;

	/**
	 * @var TimeParser
	 */
	private $timeParser;

	/**
	 * @Inject({"@Espeak", "@TimeParser"})
	 * @param Espeak $espeak
	 * @param TimeParser $time_parser
	 */
	public function __construct(Espeak $espeak, TimeParser $time_parser) {
		$this->espeak      = $espeak;
		$this->timeParser = $time_parser;
	}

	/**
	 * @return array
	 * @Route("/espeak/", name="espeak.index")
	 */
	public function index() {
		$speakers = $this->espeak->getSpeakers();
		$jobs     = $this->espeak->getPendingJobs();

		return [
			'speakers' => $speakers,
			'jobs' => $jobs
		];
	}

	/**
	 * @param Request $request
	 * @return MessageQueueJob[]
	 * @Route("/espeak/speak/", methods="POST")
	 */
	public function speak(Request $request) {
		$speaker   = $request->request->get('speaker') ?: null;
		$text      = $request->request->get('text');
		$volume    = $request->request->getInt('volume');
		$speed     = $request->request->getInt('speed');
		$delay_raw = $request->request->get('delay');

		$timestamp = $this->timeParser->parseString($delay_raw);

		$espeak_vo = new EspeakVO($text, $volume, $speed, $speaker);
		$event     = new EspeakEvent($espeak_vo);

		$this->dispatchInBackground($event, $timestamp);

		$pending_jobs = $this->espeak->getPendingJobs();

		return $pending_jobs;
	}

	/**
	 * @param Request $request
	 * @return boolean
	 * @Route("/espeak/job/delete/", name="espeak.delete", methods="POST")
	 */
	public function deleteJob(Request $request) {
		$job_id = $request->request->get('job_id');

		$this->espeak->deleteJob($job_id);

		return true;
	}
}
