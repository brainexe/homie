<?php

namespace Matze\Core\Controller;

use Silex\ControllerProviderInterface;

interface ControllerInterface extends  ControllerProviderInterface {

	/**
	 * @return string
	 */
	public function getPath();
}