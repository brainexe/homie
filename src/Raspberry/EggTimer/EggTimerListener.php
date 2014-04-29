<?php

namespace Raspberry\Espeak;

use Matze\Core\EventDispatcher\AbstractEventListener;
use Raspberry\EggTimer\EggTimer;
use Raspberry\EggTimer\EggTimerEvent;
use Raspberry\Media\Sound;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
			$dispatcher->dispatch($new_event->event_name, $new_event);
		}

		/** @var Sound $sound */
		$sound = $this->getService('Sound');
		$sound->playSound(ROOT . EggTimer::EGG_TIMER_RING_SOUND);
	}
}