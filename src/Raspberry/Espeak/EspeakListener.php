<?php

namespace Raspberry\Espeak;

use Matze\Core\EventDispatcher\AbstractEventListener;
use Matze\Core\Traits\ServiceContainerTrait;

/**
 * @EventListener
 */
class EspeakListener extends AbstractEventListener {

	use ServiceContainerTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			EspeakEvent::NAME => 'handleEspeakEvent'
		];
	}

	/**
	 * @param EspeakEvent $event
	 */
	public function handleEspeakEvent(EspeakEvent $event) {
		echo "..";
		$espeak_vo = $event->espeak;

		/** @var Espeak $espeak */
		$espeak = $this->getService('Espeak');
		$espeak->speak($espeak_vo->text, $espeak_vo->volume, $espeak_vo->speed, $espeak_vo->speaker);
	}
}