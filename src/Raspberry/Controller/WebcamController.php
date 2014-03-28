<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\IdGeneratorTrait;
use Raspberry\Webcam\Webcam;
use Raspberry\Webcam\WebcamEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

		return $this->render('webcam.html.twig', [
			'shots' => $shots
		]);
	}

	/**
	 * @Route("/webcam/take/", name="webcam.take")
	 */
	public function takePhoto() {
		$name = $this->generateRandomId();

		$event = new WebcamEvent($name);
		$this->dispatchInBackground($event);

		return new RedirectResponse('/webcam/');
	}

	/**
	 * @Route("/webcam/delete/{shot_id}/", name="webcam.delete")
	 */
	public function delete($shot_id) {
		$this->_service_webcam->delete($shot_id);

		return new RedirectResponse('/webcam/');
	}
}