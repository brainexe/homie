<?php

namespace Raspberry\Scrabble\VO;

use Matze\Core\Util\AbstractVO;

class GameVO extends AbstractVO {

	const STATUS_RUNNING = 'running';
	const STATUS_STOPPED = 'stopped';

	/**
	 * @var integer
	 */
	public $game_id;

	/**
	 * @var integer
	 */
	public $user_count;

	/**
	 * @var string
	 */
	public $user_names;

	/**
	 * @var integer
	 */
	public $current_user_idx;

	/**
	 * @var integer
	 */
	public $points_sum;

	/**
	 * @var integer
	 */
	public $created_at;

	/**
	 * @var string self::STATUS_*
	 */
	public $status;

	/**
	 * @var integer
	 */
	public $round;
} 