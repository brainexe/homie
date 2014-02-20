<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\ControllerInterface;
use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Espeak\Espeak;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Matze\Annotations\Annotations as DI;
use Matze\Core\Annotations as CoreDI;

/**
 * @CoreDI\Controller
 */
class EspeakController implements ControllerInterface {

	use EventDispatcherTrait;

	/**
	 * @var Espeak
	 */
	private $_service_espeak;

	/**
	 * @DI\Inject("@Espeak")
	 */
	public function __construct(Espeak $espeak) {
		$this->_service_espeak = $espeak;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return '/espeak/';
	}

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app) {
			$speakers = $this->_service_espeak->getSpeakers();
			return $app['twig']->render('espeak.html.twig', ['speakers' => $speakers]);
		});

		$controllers->post('/', function(Application $app, Request $request) {
			$speaker = $request->request->get('speaker');
			$text = $request->request->get('text');
			$volume = $request->request->getInt('volume');
			$speed = $request->request->getInt('speed');

			$event = new MessageQueueEvent('Espeak', 'speak', [$text, $volume, $speed, $speaker]);
			$this->getEventDispatcher()->dispatch(MessageQueueEvent::NAME, $event);

			return $app->redirect('/espeak/');
		});

		return $controllers;
	}

}