<?php

namespace Raspberry\Espeak;

use Matze\Core\Util\AbstractVO;

class EspeakVO extends AbstractVO {

	/**
	 * @var string
	 */
	public $text;

	/**
	 * @var integer
	 */
	public $volume = 100;

	/**
	 * @var integer
	 */
	public $speed = 100;

	/**
	 * @var string
	 */
	public $speaker;

	/**
	 * @param string $text
	 * @param integer $volume
	 * @param integer $speed
	 * @param string $speaker
	 */
	public function __construct($text, $volume = 100, $speed = 100, $speaker = Espeak::DEFAULT_SPEAKER) {
		$this->text = $text;
		$this->volume = $volume;
		$this->speed = $speed;
		$this->speaker = $speaker;
	}
} 