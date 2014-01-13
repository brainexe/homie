<?php

namespace Raspberry\Gpio;

use Raspberry\Client\LocalClient;
use Raspberry\Traits\PDOTrait;
use Sly\RPIManager\IO\GPIO\Manager;

class GpioManager {

	/**
	 * @var LocalClient
	 */
	private $_local_client;

	/**
	 * @param LocalClient $local_client
	 */
	public function setLocalClient(LocalClient $local_client) {
		$this->_local_client = $local_client;
	}

	public function getPins() {
		$manager = new Manager($this->_local_client);

		$demoPin = $manager->getPins()->get(7);
	}
}