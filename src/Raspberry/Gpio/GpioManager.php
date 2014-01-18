<?php

namespace Raspberry\Gpio;

use Raspberry\Client\LocalClient;
use Raspberry\Traits\PDOTrait;
use Sly\RPIManager\IO\GPIO\Collection\PinsCollection;
use Sly\RPIManager\IO\GPIO\Manager;
use Sly\RPIManager\IO\GPIO\Model\Pin;

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

	/**
	 * @return PinsCollection
	 */
	public function getPins() {
		try {
			$manager = new Manager($this->_local_client);
			return $manager->getPins();
		} catch (\RuntimeException $e) {
			$collection = new PinsCollection();

			$pin = new Pin();
			$pin->setID(2);
			$pin->setDirection('out');
			$pin->setValue(true);
			$collection->add($pin);

			$pin = new Pin();
			$pin->setID(3);
			$pin->setDirection('in');
			$pin->setValue(false);
			$collection->add($pin);

			return $collection;
		}
	}

	/**
	 * @param integer $id
	 * @param string $status
	 * @param boolean $value
	 */
	public function setPin($id, $status, $value) {
		$manager = new Manager($this->_local_client);
		$pin = $manager->getPins()->get($id);

		$pin->setDirection($status ? 'out' : 'in');
		$pin->setValue((bool)$value);

		$manager->update($pin);
	}

}