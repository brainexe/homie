<?php

namespace Raspberry\Sensors;

class SensorVO {

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
	 * @var interval
	 */
	public $interval;

	/**
	 * @var integer
	 */
	public $node;
}