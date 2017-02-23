<?php

namespace Homie\TodoList\VO;

class TodoItemVO
{

    public const STATUS_PENDING   = 'pending';
    public const STATUS_OPEN      = 'open';
    public const STATUS_PROGRESS  = 'progress';
    public const STATUS_COMPLETED = 'completed';

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
