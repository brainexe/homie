<?php

namespace Raspberry\Client;

use Sly\RPIManager\IO\GPIO\Model\Pin as SlyPin;

class Pin extends SlyPin {

	/**
	 * @var string
	 */
	protected $_description;

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->_description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->_description = $description;
	}
} 