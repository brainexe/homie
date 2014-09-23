<?php

namespace Raspberry\TodoList;

use BrainExe\Core\EventDispatcher\AbstractEventListener;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;

/**
 * @EventListener
 */
class TodoListener extends AbstractEventListener {

	use EventDispatcherTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			TodoListEvent::ADD => 'handleAddEvent'
		];
	}

	/**
	 * @param TodoListEvent $event
	 */
	public function handleAddEvent(TodoListEvent $event) {
		if ($event->item_vo->deadline) {
			$espeak_vo = new EspeakVO(sprintf('Erinnerung: %s', $event->item_vo->name));
			$espeak_event = new EspeakEvent($espeak_vo);
			$this->dispatchInBackground($espeak_event, $event->item_vo->deadline);
		}
	}
}