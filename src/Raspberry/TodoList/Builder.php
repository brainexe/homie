<?php

namespace Raspberry\TodoList;

use BrainExe\Annotations\Annotations\Service;
use Raspberry\TodoList\VO\TodoItemVO;

/**
 * @Service("TodoVoBuilder", public=false)
 */
class Builder
{

    /**
     * @param array $raw
     * @return TodoItemVO
     */
    public function build(array $raw)
    {
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
}
