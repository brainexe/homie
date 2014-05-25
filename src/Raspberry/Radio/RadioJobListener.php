<?php

namespace Raspberry\Radio;

use Matze\Core\EventDispatcher\AbstractEventListener;

/**
 * @EventListener
 */
class RadioJobListener extends AbstractEventListener {

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			RadioChangeEvent::CHANGE_RADIO => 'handleChangeEvent'
		];
	}

	/**
	 * @param RadioChangeEvent $event
	 */
	public function handleChangeEvent(RadioChangeEvent $event) {
		/** @var RadioController $radio_controller */
		$radio_controller = $this->getService('RadioController');

		$radio_controller->setStatus($event->radio_vo->code, $event->radio_vo->pin, $event->status);
	}
}