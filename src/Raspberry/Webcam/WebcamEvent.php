<?php

namespace Raspberry\Webcam;

use Matze\Core\EventDispatcher\AbstractEvent;

class WebcamEvent extends AbstractEvent {

	const NAME = 'webcam.photo';

	/**
	 * @var string
	 */
	public $file_name;

	/**
	 * @param string $file_name
	 */
	public function __construct($file_name) {
		$this->event_name = self::NAME;
		$this->file_name = $file_name;
	}
} 