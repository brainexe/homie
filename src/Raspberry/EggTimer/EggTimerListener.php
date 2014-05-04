<?php

namespace Raspberry\Espeak;

use Matze\Core\EventDispatcher\AbstractEventListener;
use Matze\Core\EventDispatcher\EventDispatcher;
use Raspberry\EggTimer\EggTimer;
use Raspberry\EggTimer\EggTimerEvent;
use Raspberry\Media\Sound;

/**
 * @EventListener
 */
class EggTimerListener extends AbstractEventListener {

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			EggTimerEvent::DONE => 'handleEggTimerEvent'
		];
	}

	/**
	 * @param EggTimerEvent $event
	 */
	public function handleEggTimerEvent(EggTimerEvent $event) {

		if ($event->espeak) {
			$new_event = new EspeakEvent($event->espeak);

			/** @var EventDispatcher $dispatcher */
			$dispatcher = $this->getService('eventdispatcher');
			$dispatcher->dispatchEvent($new_event);
		}

		/** @var Sound $sound */
		$sound = $this->getService('Sound');
		$sound->playSound(ROOT . EggTimer::EGG_TIMER_RING_SOUND);
	}
}