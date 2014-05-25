<?php

namespace Raspberry\TodoList;

use Matze\Core\Util\AbstractVO;

class TodoItemVO extends AbstractVO {

	const STATUS_PENDING = 'pending';
	const STATUS_PROGRESS = 'progress';
	const STATUS_COMPLETED = 'completed';

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var int
	 */
	public $user_id;

	/**
	 * @var int
	 */
	public $user_name;

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
	public $created_at;

	/**
	 * @var integer
	 */
	public $last_change;
}