<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Util\TimeParser;
use Raspberry\Espeak\Espeak;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
	 */
	public function __construct(Espeak $espeak, TimeParser $time_parser) {
		$this->_espeak = $espeak;
		$this->_time_parser = $time_parser;
	}

	/**
	 * @return string
	 * @Route("/espeak/", name="espeak.index")
	 */
	public function index() {
		$speakers = $this->_espeak->getSpeakers();

		return $this->renderToResponse('espeak.html.twig', [
			'speakers' => $speakers,
			'jobs' => $this->_espeak->getPendingJobs()
		]);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/espeak/speak/", methods="POST")
	 */
	public function speak(Request $request) {
		$speaker = $request->request->get('speaker');
		$text = $request->request->get('text');
		$volume = $request->request->getInt('volume');
		$speed = $request->request->getInt('speed');
		$delay_raw = $request->request->get('delay');

		$timestamp = $this->_time_parser->parseString($delay_raw);

		$espeak_vo = new EspeakVO($text, $volume, $speed, $speaker);
		$event = new EspeakEvent($espeak_vo);

		$this->dispatchInBackground($event, $timestamp);

		return new RedirectResponse('/espeak/');
	}

	/**
	 * @param string $job_id
	 * @return RedirectResponse
	 * @Route("/espeak/job/delete/{job_id}/", name="espeak.delete", csrf=true)
	 */
	public function deleteJobJob($job_id) {
		$this->_espeak->deleteJob($job_id);

		return new RedirectResponse('/espeak/');
	}
}