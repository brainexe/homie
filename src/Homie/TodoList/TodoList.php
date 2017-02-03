<?php

namespace Homie\TodoList;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\TimeTrait;
use Generator;
use Homie\TodoList\Exception\ItemNotFoundException;
use Homie\TodoList\VO\TodoItemVO;

/**
 * @Service("TodoList", public=false)
 */
class TodoList
{
    use EventDispatcherTrait;
    use IdGeneratorTrait;
    use TimeTrait;

    /**
     * @var TodoListGateway
     */
    private $gateway;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @Inject({"@TodoListGateway", "@TodoList.Builder"})
     * @param TodoListGateway $gateway
     * @param Builder $builder
     */
    public function __construct(TodoListGateway $gateway, Builder $builder)
    {
        $this->gateway = $gateway;
        $this->builder = $builder;
    }

    /**
     * @param UserVO $user
     * @param TodoItemVO $itemVo
     * @return TodoItemVO
     */
    public function addItem(UserVO $user, TodoItemVO $itemVo) : TodoItemVO
    {
        $now = $this->now();

        $itemVo->todoId    = $this->generateUniqueId('todolist');
        $itemVo->userId    = $user->id;
        $itemVo->userName  = $user->username;
        $itemVo->createdAt = $itemVo->lastChange = $now;
        $itemVo->status    = $itemVo->status ?: TodoItemVO::STATUS_OPEN;
        if ($itemVo->deadline < $now) {
            $itemVo->deadline = 0;
        }

        $this->gateway->addItem($itemVo);

        $event = new TodoListEvent($itemVo, TodoListEvent::ADD);
        $this->dispatchEvent($event);

        return $itemVo;
    }

    /**
     * @return Generator|TodoItemVO[]
     */
    public function getList()
    {
        $rawList = $this->gateway->getList();

        foreach ($rawList as $item) {
            yield $this->builder->build($item);
        }
    }

    /**
     * @param int $itemId
     * @return TodoItemVO
     * @throws ItemNotFoundException
     */
    public function getItem(int $itemId) : TodoItemVO
    {
        $raw = $this->gateway->getRawItem($itemId);

        if (empty($raw)) {
            throw new ItemNotFoundException(sprintf('Invalid Todo list item: %d', $itemId));
        }

        return $this->builder->build($raw);
    }

    /**
     * @param int $itemId
     * @param array $changes
     * @return TodoItemVO
     */
    public function editItem(int $itemId, array $changes) : TodoItemVO
    {
        $itemVo = $this->getItem($itemId);

        $this->gateway->editItem($itemId, $changes);

        $event = new TodoListEvent($itemVo, TodoListEvent::EDIT, $changes);
        $this->dispatchEvent($event);

        return $itemVo;
    }

    /**
     * @param int $itemId
     */
    public function deleteItem(int $itemId)
    {
        $itemVo = $this->getItem($itemId);

        $this->gateway->deleteItem($itemId);

        $event = new TodoListEvent($itemVo, TodoListEvent::REMOVE);
        $this->dispatchEvent($event);
    }
}
