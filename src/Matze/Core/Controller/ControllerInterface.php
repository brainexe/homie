<?php

namespace Raspberry\Controller;

use Silex\ControllerProviderInterface;

interface ControllerInterface extends  ControllerProviderInterface {

	/**
	 * @return string
	 */
	public function getPath();
} 