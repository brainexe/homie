<?php

namespace Raspberry\EggTimer;

use Matze\Core\EventDispatcher\AbstractEvent;
use Raspberry\Espeak\EspeakVO;

class EggTimerEvent extends AbstractEvent {

	const DONE = 'egg_timer.done';

	/**
	 * @var EspeakVO
	 */
	public $espeak;

	/**
	 * @param EspeakVO $espeak
	 */
	function __construct(EspeakVO $espeak = null) {
		$this->event_name = self::DONE;
		$this->espeak = $espeak;
	}

} 