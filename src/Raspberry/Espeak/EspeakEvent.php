<?php

namespace Raspberry\Espeak;

use Matze\Core\EventDispatcher\AbstractEvent;

class EspeakEvent extends AbstractEvent {

	const NAME = 'espeak.speak';

	/**
	 * @var EspeakVO
	 */
	public $espeak;

	/**
	 * @param EspeakVO $espeak
	 */
	function __construct(EspeakVO $espeak) {
		$this->event_name = self::NAME;
		$this->espeak = $espeak;
	}

} 