<?php

namespace Raspberry\Gpio;

use InvalidArgumentException;

class PinsCollection {
	/**
	 * @var Pin[]
	 */
	private $_pins = [];

	/**
	 * @param Pin $pin
	 */
	public function add(Pin $pin) {
		$pin_id = $pin->getID();
		$this->_pins[$pin_id] = $pin;
	}

	/**
	 * @param integer $id
	 * @return Pin
	 * @throws InvalidArgumentException
	 */
	public function get($id) {
		if (empty($this->_pins[$id])) {
			throw new InvalidArgumentException(sprintf('Pin #%s does not exist', $id));
		}

		return $this->_pins[$id];
	}

	/**
	 * @return Pin[]
	 */
	public function getAll() {
		return $this->_pins;
	}
}
