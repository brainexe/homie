<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Espeak\Espeak;
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
	 */
	public function getRoutes() {
		return [
			'espeak.index' => [
				'pattern' => '/espeak/',
				'defaults' =>  ['_controller' =>  'Espeak::index']
			],
			'espeak.speak' => [
				'pattern' => '/espeak/speak/',
				'defaults' =>  ['_controller' =>  'Espeak::speak']
				// TODO require post
			]
		];
	}

	public function index() {
		$speakers = $this->_service_espeak->getSpeakers();
		return $this->render('espeak.html.twig', ['speakers' => $speakers]);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function speak(Request $request) {
		$speaker = $request->request->get('speaker');
		$text = $request->request->get('text');
		$volume = $request->request->getInt('volume');
		$speed = $request->request->getInt('speed');

		$event = new MessageQueueEvent('Espeak', 'speak', [$text, $volume, $speed, $speaker]);
		$this->getEventDispatcher()->dispatch(MessageQueueEvent::NAME, $event);

		return new RedirectResponse('/espeak/');
	}
}