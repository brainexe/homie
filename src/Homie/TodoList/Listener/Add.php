<?php

namespace Homie\TodoList\Listener;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Homie\TodoList\TodoListEvent;

/**
 * @EventListener("TodoList.Listener.Add")
 */
class Add
{

    use EventDispatcherTrait;

    /**
     * @Listen(TodoListEvent::ADD)
     * @param TodoListEvent $event
     */
    public function handleAddEvent(TodoListEvent $event)
    {
        $itemVo = $event->getItemVo();

        if (!empty($itemVo->deadline)) {
            $espeakVo    = new EspeakVO(sprintf(_('Erinnerung: %s'), $itemVo->name));
            $espeakEvent = new EspeakEvent($espeakVo);
            $this->dispatchInBackground($espeakEvent, $itemVo->deadline);
        }
    }
}
