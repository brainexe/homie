<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\IdGeneratorTrait;
use Raspberry\Webcam\Webcam;
use Raspberry\Webcam\WebcamEvent;

/**
 * @Controller
 */
class WebcamController extends AbstractController {

	use EventDispatcherTrait;
	use IdGeneratorTrait;

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
	 * @Route("/webcam/", name="webcam.index")
	 */
	public function index() {
		$shots = $this->_service_webcam->getPhotos();

		return $this->render('webcam.html.twig', ['shots' => $shots]);
	}

	public function takePhoto() {
		$name = $this->generateRandomId();

		$event = new WebcamEvent($name);
		$this->dispatchInBackground($event, 10);

	}
}