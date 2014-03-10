<?php

namespace Raspberry\Radio;

use Matze\Core\EventDispatcher\AbstractEventListener;
use Matze\Core\Traits\ServiceContainerTrait;

/**
 * @EventListener
 */
class RadioJobListener extends AbstractEventListener {

	use ServiceContainerTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			RadioChangeEvent::NAME => 'handleChangeEvent'
		];
	}

	/**
	 * @param RadioChangeEvent $event
	 */
	public function handleChangeEvent(RadioChangeEvent $event) {
		/** @var RadioController $radio_controller */
		$radio_controller = $this->getService('RadioController');

		$radio_controller->setStatus($event->radio_vo->code, $event->radio_vo->pin, $event->status);

		if ($event->is_job) {
			/** @var RadioJobGateway $radio_job_gateway */
			$radio_job_gateway = $this->getService('RadioJobGateway');
			$radio_job_gateway->deleteJob($event->radio_vo->id, $event->status);
		}
	}
}