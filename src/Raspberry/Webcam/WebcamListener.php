<?php

namespace Raspberry\Webcam;
use Matze\Core\EventDispatcher\AbstractEventListener;
use Matze\Core\Traits\ServiceContainerTrait;

/**
 * @EventListener
 */
class WebcamListener extends AbstractEventListener {

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			WebcamEvent::NAME => 'handleWebcamEvent'
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