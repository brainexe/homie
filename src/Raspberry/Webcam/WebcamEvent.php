<?php

namespace Raspberry\Webcam;

use Matze\Core\EventDispatcher\AbstractEvent;

class WebcamEvent extends AbstractEvent {

	const NAME = 'webcam.photo';

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @param string $name
	 */
	public function __construct($name) {
		$this->event_name = self::NAME;
		$this->name = $name;
	}
} 