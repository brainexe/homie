<?php

namespace Raspberry\TodoList\VO;

use BrainExe\Core\Util\AbstractVO;

class TodoItemVO extends AbstractVO
{

    const STATUS_PENDING = 'pending';
    const STATUS_PROGRESS = 'progress';
    const STATUS_COMPLETED = 'completed';

    /**
     * @var integer
     */
    public $todoId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $userName;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string self::STATUS_*
     */
    public $status;

    /**
     * @var int
     */
    public $deadline;

    /**
     * @var integer
     */
    public $createdAt;

    /**
     * @var integer
     */
    public $lastChange;
}
