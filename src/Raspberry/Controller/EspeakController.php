<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\ControllerInterface;
use Raspberry\Espeak\Espeak;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(name="Controller.EspeakController", public=false, tags={{"name" = "controller"}})
 */
class EspeakController implements ControllerInterface {

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

			$this->_service_espeak->speak($text, $volume, $speed, $speaker);

			return $app->redirect('/espeak/');
		});

		return $controllers;
	}

}