<?php

namespace Raspberry\Controller;

use Matze\Core\Authentication\AbstractAuthenticationController;
use Symfony\Component\HttpFoundation\Request;

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
		return $this->render('authentication/register.html.twig');
	}
} 