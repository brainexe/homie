<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\TwigTrait;
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
		return $this->renderToResponse('layout.html.twig', [
			'current_user' => $request->attributes->get('user')
		]);
	}
}
