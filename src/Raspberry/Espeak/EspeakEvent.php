<?php

namespace Raspberry\Espeak;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocketInterface;

class EspeakEvent extends AbstractEvent implements PushViaWebsocketInterface {

	const SPEAK = 'espeak.speak';

	/**
	 * @var EspeakVO
	 */
	public $espeak;

	/**
	 * @param EspeakVO $espeak
	 */
	function __construct(EspeakVO $espeak) {
		$this->event_name = self::SPEAK;
		$this->espeak = $espeak;
	}

} 