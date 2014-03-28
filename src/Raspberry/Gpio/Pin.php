<?php

namespace Raspberry\Gpio;

class Pin {

	/**
	 * @var string
	 */
	protected $_description;

	const DIRECTION_IN = 'in';
	const DIRECTION_OUT = 'out';

	const VALUE_LOW = 'LOW';
	const VALUE_HIGH = 'HIGH';

	/**
	 * wiringPi ID.
	 *
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $direction;

	/**
	 * @var boolean
	 */
	private $value;

	/**
	 * @return Pin
	 */
	public function __construct() {
		return $this->getName() ? : $this->getId();
	}

	/**
	 * Get ID value.
	 *
	 * @return integer
	 */
	public function getID() {
		return $this->id;
	}

	/**
	 * Set ID value.
	 *
	 * @param integer $id ID
	 *
	 * @return Pin
	 */
	public function setID($id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Get Name value.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set Name value.
	 *
	 * @param string $name Name
	 *
	 * @return Pin
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get Direction value.
	 *
	 * @return string
	 */
	public function getDirection() {
		return $this->direction;
	}

	/**
	 * Set Direction value.
	 *
	 * @param string $direction Direction
	 *
	 * @return Pin
	 */
	public function setDirection($direction) {
		$this->direction = $direction;

		return $this;
	}

	/**
	 * Get Value value.
	 *
	 * @return boolean
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Set Value value.
	 *
	 * @param boolean $value Value
	 *
	 * @return Pin
	 */
	public function setValue($value) {
		$this->value = $value;

		return $this;
	}

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