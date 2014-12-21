<?php

namespace Raspberry\TodoList;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\TimeTrait;
use Raspberry\TodoList\VO\TodoItemVO;

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
     * @Inject({"@TodoListGateway"})
     * @param TodoListGateway $gateway
     */
    public function __construct(TodoListGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param UserVO $user
     * @param TodoItemVO $itemVo
     * @return TodoItemVO
     */
    public function addItem(UserVO $user, TodoItemVO $itemVo)
    {
        $now = $this->now();

        $itemVo->todoId    = $this->generateRandomNumericId();
        $itemVo->userId    = $user->id;
        $itemVo->userName  = $user->username;
        $itemVo->createdAt = $itemVo->lastChange = $now;
        $itemVo->status    = TodoItemVO::STATUS_PENDING;
        if ($itemVo->deadline < $now) {
            $itemVo->deadline = 0;
        }

        $this->gateway->addItem($itemVo);

        $event = new TodoListEvent($itemVo, TodoListEvent::ADD);
        $this->dispatchEvent($event);

        return $itemVo;
    }

    /**
     * @return TodoItemVO[]
     */
    public function getList()
    {
        $list = [];
        $rawList = $this->gateway->getList();

        foreach ($rawList as $item) {
            $itemVo = new TodoItemVO();
            $itemVo->fillValues($item);
            $list[$item['todoId']] = $itemVo;
        }

        return $list;
    }

    /**
     * @param integer $itemId
     * @return null|TodoItemVO
     */
    public function getItem($itemId)
    {
        $raw = $this->gateway->getRawItem($itemId);

        if (empty($raw)) {
            return null;
        }

        $itemVo = new TodoItemVO();
        $itemVo->todoId      = $raw['todoId'];
        $itemVo->name        = $raw['name'];
        $itemVo->userId      = $raw['userId'];
        $itemVo->userName    = $raw['userName'];
        $itemVo->description = $raw['description'];
        $itemVo->status      = $raw['status'];
        $itemVo->deadline    = $raw['deadline'];
        $itemVo->createdAt   = $raw['createdAt'];
        $itemVo->lastChange  = $raw['lastChange'];

        return $itemVo;
    }

    /**
     * @param int $itemId
     * @param array $changes
     * @return TodoItemVO
     */
    public function editItem($itemId, array $changes)
    {
        $this->gateway->editItem($itemId, $changes);

        $itemVo = $this->getItem($itemId);

        $event = new TodoListEvent($itemVo, TodoListEvent::EDIT);
        $this->dispatchEvent($event);

        return $itemVo;
    }

    /**
     * @param int $itemId
     */
    public function deleteItem($itemId)
    {
        $itemVo = $this->getItem($itemId);

        $this->gateway->deleteItem($itemId);

        $event = new TodoListEvent($itemVo, TodoListEvent::REMOVE);
        $this->dispatchEvent($event);
    }
}
