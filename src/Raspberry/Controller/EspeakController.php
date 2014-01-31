<?php

namespace Raspberry\Controller;

use Raspberry\Espeak\Espeak;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class EspeakController implements ControllerProviderInterface {

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app) {
			$speakers = Espeak::getSpeakers();
			return $app['twig']->render('espeak.html.twig', ['speakers' => $speakers]);
		});

		$controllers->post('/', function(Application $app, Request $request) {
			/** @var Espeak $espeak */
			$espeak = $app['dic']->get('Espeak');

			$speaker = $request->request->get('speaker');
			$text = $request->request->get('text');
			$volume = $request->request->getInt('volume');
			$speed = $request->request->getInt('speed');

			$espeak->speak($text, $volume, $speed, $speaker);

			return $app->redirect('/espeak/');
		});

		return $controllers;
	}

}