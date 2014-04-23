<?php

namespace Raspberry\Controller;

use Matze\Core\Authentication\AbstractAuthenticationController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller
 */
class AuthenticationController extends AbstractAuthenticationController {

	/**
	 * {@inheritdoc}
	 * @Route("/login/", name="authenticate.login", methods="GET")
	 */
	public function loginForm(Request $request) {
		return $this->render('authentication/login.html.twig');
	}

	/**
	 * {@inheritdoc}
	 * @Route("/register/", name="authenticate.register", methods="GET")
	 */
	public function registerForm(Request $request) {
		$token = $request->query->get('token');

		$response = new Response();

		$response->setContent($this->render('authentication/register.html.twig', [
			'token' => $token
		]));

		if ($token && !$request->cookies->has('token')) {
			$cookie = new Cookie('token', $token);
			$response->headers->setCookie($cookie);
		}

		return $response;
	}
} 