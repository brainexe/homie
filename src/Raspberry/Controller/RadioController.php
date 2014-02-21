<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\ControllerInterface;
use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Predis\Client;
use Raspberry\Radio\Radios;
use Silex\Application;
use Matze\Annotations\Annotations as DI;
use Matze\Core\Annotations as CoreDI;

/**
 * @CoreDI\Controller
 */
class RadioController implements ControllerInterface {

	use EventDispatcherTrait;

	/**
	 * @var Radios;
	 */
	private $_service_radios;

	/**
	 * @return string
	 */
	public function getPath() {
		return '/radio/';
	}

	/**
	 * @DI\Inject("@Radios")
	 */
	public function __construct(Radios $radios) {
		$this->_service_radios = $radios;
	}

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app) {
			$radios_formatted = $this->_service_radios->getRadios();

			return $app['twig']->render('radio.html.twig', ['radios' => $radios_formatted ]);
		});

		$controllers->get('/{id}/{status}/', function($id, $status, Application $app) {
			$radio = $this->_service_radios->getRadios()[$id];

			$event = new MessageQueueEvent('RadioController', 'setStatus', [$radio['code'], $radio['pin'], $status]);
			$this->getEventDispatcher()->dispatch(MessageQueueEvent::NAME, $event);

			return $app->redirect('/radio/');
		});

		return $controllers;
	}
}