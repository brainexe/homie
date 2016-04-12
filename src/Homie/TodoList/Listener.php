<?php

namespace Homie\TodoList;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("TodoListener")
 */
class Listener implements EventSubscriberInterface
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
        $itemVo = $event->getItemVo();

        if ($itemVo->deadline) {
            $espeakVo    = new EspeakVO(sprintf(_('Erinnerung: %s'), $itemVo->name));
            $espeakEvent = new EspeakEvent($espeakVo);
            $this->dispatchInBackground($espeakEvent, $itemVo->deadline);
        }
    }
}
