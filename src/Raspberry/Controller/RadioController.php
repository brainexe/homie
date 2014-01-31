<?php

namespace Raspberry\Controller;

use Predis\Client;
use Raspberry\Radio\Radios;
use Silex\Application;
use Silex\ControllerProviderInterface;

class RadioController implements ControllerProviderInterface {

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app) {
			/** @var Radios $radios */
			$radios = $app['dic']->get('Radios');

			$radios_formatted = $radios->getRadios();

			return $app['twig']->render('radio.html.twig', ['radios' => $radios_formatted ]);
		});

		$controllers->get('/{id}/{status}/', function($id, $status, Application $app) {
			/** @var Radios $radios */
			/** @var Client $predis */
			$predis = $app['dic']->get('Predis');
			$radios = $app['dic']->get('Radios');

			$radio = $radios->getRadios()[$id];
			$radio['status'] = $status;

			$predis->PUBLISH('radio_changes', serialize($radio));

			return $app->redirect('/radio/');
		});

		return $controllers;
	}
}