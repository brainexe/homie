<?php

namespace Raspberry\Espeak;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class EspeakListener implements EventSubscriberInterface {

	/**
	 * @var Espeak
	 */
	private $espeak;

	/**
	 * @inject("@espeak")
	 * @param Espeak $espeak
	 */
	public function __construct(Espeak $espeak) {
		$this->espeak = $espeak;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			EspeakEvent::SPEAK => 'handleEspeakEvent'
		];
	}

	/**
	 * @param EspeakEvent $event
	 */
	public function handleEspeakEvent(EspeakEvent $event) {
		$espeak_vo = $event->espeak;

		$this->espeak->speak($espeak_vo->text, $espeak_vo->volume, $espeak_vo->speed, $espeak_vo->speaker);
	}
}
