<?php

namespace Homie\TodoList;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\JsonSerializableTrait;
use Homie\TodoList\VO\TodoItemVO;

class TodoListEvent extends AbstractEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;

    const ADD     = 'todo.add';
    const REMOVE  = 'todo.remove';
    const EDIT    = 'todo.edit'; // any edit action

    // when cron expression was reached in pending state -> move to open + espeak
    const PENDING = 'todo.pending';

    /**
     * @var TodoItemVO
     */
    private $itemVo;

    /**
     * @var array
     */
    private $changes;

    /**
     * @param TodoItemVO $itemVo
     * @param string $eventName
     * @param array $changes
     */
    public function __construct(TodoItemVO $itemVo, string $eventName, array $changes = [])
    {
        parent::__construct($eventName);

        $this->itemVo  = $itemVo;
        $this->changes = $changes;
    }

    /**
     * @return TodoItemVO
     */
    public function getItemVo() : TodoItemVO
    {
        return $this->itemVo;
    }

    /**
     * @return array
     */
    public function getChanges() : array
    {
        return $this->changes;
    }
}
