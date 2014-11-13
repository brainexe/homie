<?php

namespace Raspberry\Espeak;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Raspberry\EggTimer\EggTimer;
use Raspberry\EggTimer\EggTimerEvent;
use Raspberry\Media\Sound;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class EggTimerListener implements EventSubscriberInterface {

	use EventDispatcherTrait;

	/**
	 * @var Sound
	 */
	private $_sound;

	/**
	 * @inject("@Sound")
	 * @param Sound $sound
	 */
	public function __construct(Sound $sound) {
		$this->_sound = $sound;
	}

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

			$this->dispatchEvent($new_event);
		}

		$this->_sound->playSound(ROOT . EggTimer::EGG_TIMER_RING_SOUND);
	}
}