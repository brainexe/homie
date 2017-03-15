<?php

namespace Homie\TodoList\Listener;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\Cron\Expression;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Translation\TranslationTrait;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Homie\TodoList\Exception\ItemNotFoundException;
use Homie\TodoList\TodoList;
use Homie\TodoList\TodoListEvent;
use Homie\TodoList\VO\TodoItemVO;

/**
 * @EventListener
 */
class Pending
{

    use EventDispatcherTrait;
    use TranslationTrait;

    /**
     * @var TodoList
     */
    private $todoList;

    /**
     * @var Expression
     */
    private $cron;

    /**
     * @param TodoList $todoList
     * @param Expression $cron
     */
    public function __construct(TodoList $todoList, Expression $cron)
    {
        $this->todoList = $todoList;
        $this->cron     = $cron;
    }

    /**
     * @Listen(TodoListEvent::EDIT)
     * @param TodoListEvent $event
     */
    public function handleEditEvent(TodoListEvent $event)
    {
        $itemVo   = $event->getItemVo();
        $newState = $event->getChanges()['status'] ?? '';

        if (TodoItemVO::STATUS_PENDING === $newState && !empty($itemVo->cronExpression)) {
            $pendingTime = $this->cron->getNextRun($itemVo->cronExpression);

            $newEvent = new TodoListEvent($itemVo, TodoListEvent::PENDING);
            $this->dispatchInBackground($newEvent, $pendingTime);
        }
    }
    /**
     * @Listen(TodoListEvent::PENDING)
     * @param TodoListEvent $event
     */
    public function handlePendingEvent(TodoListEvent $event)
    {
        $oldItem = $event->getItemVo();
        try {
            $currentItem = $this->todoList->getItem($oldItem->todoId);
        } catch (ItemNotFoundException $e) {
            return;
        }

        if (TodoItemVO::STATUS_PENDING === $currentItem->status) {
            $espeakVo = new EspeakVO(
                $this->translate('Todo list item %s is open again', $currentItem->name)
            );
            $espeakEvent = new EspeakEvent($espeakVo);
            $this->dispatchInBackground($espeakEvent);

            $this->todoList->editItem($currentItem->todoId, [
                'status' => TodoItemVO::STATUS_OPEN
            ]);
        }
    }
}
