<?php

namespace Homie\TodoList\VO;

class TodoItemVO
{

    const STATUS_PENDING   = 'pending';
    const STATUS_OPEN      = 'open';
    const STATUS_PROGRESS  = 'progress';
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
     * @var string
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
     * @var int
     */
    public $createdAt;

    /**
     * @var string
     */
    public $cronExpression;

    /**
     * @var int
     */
    public $lastChange;
}
