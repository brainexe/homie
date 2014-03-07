<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Raspberry\Webcam\Webcam;

/**
 * @Controller
 */
class WebcamController extends AbstractController {

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
	 * @Route("webcam", name="webcam.index")
	 */
	public function index() {
		$shots = $this->_service_webcam->getPhotos();

		return $this->render('webcam.html.twig', ['shots' => $shots]);
	}
}