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
	 */
	public function loginForm(Request $request) {
		return $this->render('authentication/login.html.twig');
	}

	/**
	 * {@inheritdoc}
	 */
	public function registerForm(Request $request) {
		return $this->render('authentication/register.html.twig');
	}
} 