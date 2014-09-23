<?php

namespace Raspberry\Controller;

use BrainExe\Core\Controller\AbstractController;
use BrainExe\Core\Traits\TwigTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class IndexController extends AbstractController {

	use TwigTrait;

	/**
	 * @param Request $request
	 * @return Response
	 * @Route("/", name="index")
	 */
	public function index(Request $request) {
		$response = $this->renderToResponse('layout.html.twig', [
			'current_user' => $request->attributes->get('user')
		]);

		// todo
		// $response->headers->set('Access-Control-Allow-Origin', '*');

		return $response;
	}

	/**
	 * @Value("debug")
	 */
	public function setTest($dsd) {

	}
}
