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
	private $_service_espeak;

	/**
	 * @var TimeParser
	 */
	private $_time_parser;

	/**
	 * @Inject({"@Espeak", "@TimeParser"})
	 */
	public function __construct(Espeak $espeak, TimeParser $time_parser) {
		$this->_service_espeak = $espeak;
		$this->_time_parser = $time_parser;
	}

	/**
	 * @return string
	 * @Route("/espeak/", name="espeak.index")
	 */
	public function index() {
		$speakers = $this->_service_espeak->getSpeakers();

		return $this->render('espeak.html.twig', [
			'speakers' => $speakers
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
}