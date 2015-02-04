<?php

namespace Raspberry\TodoList;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class TodoListener implements EventSubscriberInterface
{

    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
        TodoListEvent::ADD => 'handleAddEvent'
        ];
    }

    /**
     * @param TodoListEvent $event
     */
    public function handleAddEvent(TodoListEvent $event)
    {
        if ($event->item_vo->deadline) {
            $espeakVo = new EspeakVO(sprintf('Erinnerung: %s', $event->item_vo->name));
            $espeakEvent = new EspeakEvent($espeakVo);
            $this->dispatchInBackground($espeakEvent, $event->item_vo->deadline);
        }
    }
}
