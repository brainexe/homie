<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\ControllerInterface;
use Predis\Client;
use Raspberry\Radio\Radios;
use Silex\Application;
use Matze\Annotations\Annotations as DI;
use Matze\Core\Annotations as CoreDI;

/**
 * @CoreDI\Controller
 */
class RadioController implements ControllerInterface {

	/**
	 * @var Radios;
	 */
	private $_service_radios;

	/**
	 * @var Client
	 */
	private $_service_predis;

	/**
	 * @return string
	 */
	public function getPath() {
		return '/radio/';
	}

	/**
	 * @DI\Inject({"@Radios", "@Predis"})
	 */
	public function __construct(Radios $radios, Client $predis) {
		$this->_service_radios = $radios;
		$this->_service_predis = $predis;
	}

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app) {
			$radios_formatted = $this->_service_radios->getRadios();

			return $app['twig']->render('radio.html.twig', ['radios' => $radios_formatted ]);
		});

		$controllers->get('/{id}/{status}/', function($id, $status, Application $app) {
			$radio = $this->_service_radios->getRadios()[$id];
			$radio['status'] = $status;

			$this->_service_predis->PUBLISH('radio_changes', serialize($radio));

			return $app->redirect('/radio/');
		});

		return $controllers;
	}
}