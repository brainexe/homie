<?php

namespace Raspberry\Radio;

use Raspberry\Client\LocalClient;
use Sly\RPIManager\IO\RadioGPIO\Collection\PointsCollection;
use Sly\RPIManager\IO\RadioGPIO\Manager;
use Sly\RPIManager\IO\RadioGPIO\Model\Point;

class RadioController {
	const STATUS_ENABLED = 'enabled';
	const STATUS_DISABLED = 'disabled';
	const STATUS_UNKNOWN = 'unknown';

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

	/**
	 * @todo
	 * @param string $pin
	 * @param boolean $status
	 */
	public function setStatus($pin, $status) {

		$points = new PointsCollection();

		$point = new Point('light1');
		$point->setName('Light 1');
		$point->setCode('00100');
		$point->setNumber(2);

		$points->add($point);

		$manager = new Manager($this->_local_client, $points);

		var_dump($manager->getPoints());
		var_dump($manager->getPoints()->get('light1'));

		$manager->switchOff('light1');
	}

	/**
	 * @todo
	 * @param string $pin
	 * @return string
	 */
	public function getStatus($pin) {
		return self::STATUS_UNKNOWN;
	}
} 