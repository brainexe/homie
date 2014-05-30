<?php

namespace Raspberry\Espeak;

use Matze\Core\EventDispatcher\AbstractEvent;
use Matze\Core\EventDispatcher\PushViaWebsocketInterface;

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