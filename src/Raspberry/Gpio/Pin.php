<?php

namespace Raspberry\Gpio;

use JsonSerializable;

class Pin implements JsonSerializable {

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
	private $_id;

	/**
	 * @var string
	 */
	private $_name;

	/**
	 * @var string
	 */
	private $_direction;

	/**
	 * @var boolean
	 */
	protected $_value;

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
		return $this->_id;
	}

	/**
	 * Set ID value.
	 *
	 * @param integer $id ID
	 *
	 * @return Pin
	 */
	public function setID($id) {
		$this->_id = $id;

		return $this;
	}

	/**
	 * Get Name value.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Set Name value.
	 *
	 * @param string $name Name
	 *
	 * @return Pin
	 */
	public function setName($name) {
		$this->_name = $name;

		return $this;
	}

	/**
	 * Get Direction value.
	 *
	 * @return string
	 */
	public function getDirection() {
		return $this->_direction;
	}

	/**
	 * Set Direction value.
	 *
	 * @param string $direction Direction
	 *
	 * @return Pin
	 */
	public function setDirection($direction) {
		$this->_direction = $direction;

		return $this;
	}

	/**
	 * Get Value value.
	 *
	 * @return boolean
	 */
	public function getValue() {
		return $this->_value;
	}

	/**
	 * Set Value value.
	 *
	 * @param boolean $value Value
	 *
	 * @return Pin
	 */
	public function setValue($value) {
		$this->_value = $value;

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

	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize() {
		return [
			'id' => $this->_id,
			'value' => $this->_value,
			'name' => $this->_name,
			'description' => $this->_description,
			'direction' => $this->_direction == 'OUT' ? 1 : 0,
		];
	}
} 
