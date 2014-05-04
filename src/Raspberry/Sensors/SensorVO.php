<?php

namespace Raspberry\Sensors;

class SensorVO {

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $pin;

	/**
	 * @var int
	 */
	public $interval;

	/**
	 * @var integer
	 */
	public $node;

	/**
	 * @var int - unix timestamp
	 */
	public $last_value_timestamp;

	/**
	 * @var float
	 */
	public $last_value;
}