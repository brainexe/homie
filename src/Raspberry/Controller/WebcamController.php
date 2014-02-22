<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\ControllerInterface;
use Raspberry\Webcam\Webcam;
use Silex\Application;

/**
 * @Controller
 */
class WebcamController implements ControllerInterface {

	/**
	 * @var Webcam
	 */
	private $_service_webcam;

	/**
	 * @Inject("@Webcam")
	 */
	public function __construct(Webcam $webcam) {
		$this->_service_webcam = $webcam;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return '/webcam/';
	}

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function (Application $app) {
			$shots = $this->_service_webcam->getPhotos();
			return $app['twig']->render('webcam.html.twig', ['shots' => $shots]);
		});

		return $controllers;
	}
}