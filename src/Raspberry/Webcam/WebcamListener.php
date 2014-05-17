<?php

namespace Raspberry\Webcam;

use Matze\Core\EventDispatcher\AbstractEventListener;

/**
 * @EventListener
 */
class WebcamListener extends AbstractEventListener {

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			WebcamEvent::TAKE_PHOTO => 'handleWebcamEvent'
		];
	}

	/**
	 * @param WebcamEvent $event
	 */
	public function handleWebcamEvent(WebcamEvent $event) {
		/** @var Webcam $webcam */
		$webcam = $this->getService('Webcam');
		$webcam->takePhoto($event->name);
	}
}