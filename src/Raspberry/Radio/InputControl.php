<?php

namespace Raspberry\Radio;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @InputControl(name="radio")
 */
class InputControl implements EventSubscriberInterface {

	use EventDispatcherTrait;

	/**
	 * @var Radios
	 */
	private $radios;

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			'/^radio (on|off) (\d)$/' => 'setRadio'
		];
	}

	/**
	 * @inject("@Radios")
	 * @param Radios $radios
	 */
	public function __construct(Radios $radios) {
		$this->radios = $radios;
	}

	/**
	 * @param Event $event
	 */
	public function setRadio(Event $event) {
		list ($status, $radio_id) = $event->matches;

		$status = $status === 'on';

		$radio_vo = $this->radios->getRadio($radio_id);

		$event = new RadioChangeEvent($radio_vo, $status);
		$this->dispatchEvent($event);
	}
}
