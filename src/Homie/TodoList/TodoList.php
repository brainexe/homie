<?php

namespace Homie\TodoList;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\TimeTrait;
use Generator;
use Homie\TodoList\VO\TodoItemVO;

/**
 * @Service(public=false)
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

        $itemVo->todoId    = $this->generateUniqueId();
        $itemVo->userId    = $user->id;
        $itemVo->userName  = $user->username;
        $itemVo->createdAt = $itemVo->lastChange = $now;
        $itemVo->status    = TodoItemVO::STATUS_OPEN;
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
            $itemVo = $this->builder->build($item);
            yield $itemVo;
        }
    }

    /**
     * @param int $itemId
     * @return null|TodoItemVO
     */
    public function getItem(int $itemId)
    {
        $raw = $this->gateway->getRawItem($itemId);

        if (empty($raw)) {
            return null;
        }

        return $this->builder->build($raw);
    }

    /**
     * @param int $itemId
     * @param array $changes
     * @return TodoItemVO|null
     */
    public function editItem(int $itemId, array $changes)
    {
        $itemVo = $this->getItem($itemId);
        if (empty($itemVo)) {
            return null;
        }

        $this->gateway->editItem($itemId, $changes);

        $event = new TodoListEvent($itemVo, TodoListEvent::EDIT);
        $this->dispatchEvent($event);

        return $itemVo;
    }

    /**
     * @param int $itemId
     */
    public function deleteItem(int $itemId)
    {
        $itemVo = $this->getItem($itemId);

        if (empty($itemVo)) {
            return;
        }

        $this->gateway->deleteItem($itemId);

        $event = new TodoListEvent($itemVo, TodoListEvent::REMOVE);
        $this->dispatchEvent($event);
    }
}
