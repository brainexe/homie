<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\EventDispatcherTrait;
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
	 * @Inject("@Espeak")
	 */
	public function __construct(Espeak $espeak) {
		$this->_service_espeak = $espeak;
	}

	/**
	 * @return string
	 * @Route("/espeak/", name="espeak.index")
	 */
	public function index() {
		$speakers = $this->_service_espeak->getSpeakers();
		return $this->render('espeak.html.twig', ['speakers' => $speakers]);
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
		$delay = $request->request->get('delay');

		$espeak_vo = new EspeakVO($text, $volume, $speed, $speaker);
		$event = new EspeakEvent($espeak_vo);

		$this->dispatchInBackground($event, $delay);

		return new RedirectResponse('/espeak/');
	}
}