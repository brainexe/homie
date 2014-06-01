<?php

namespace Raspberry\Scrabble;

use Matze\Core\Traits\IdGeneratorTrait;
use Raspberry\Scrabble\VO\GameVO;

/**
 * @Service(public=false)
 */
class ScrabbleGame {

	use IdGeneratorTrait;

	/**
	 * @var ScrabbleGateway
	 */
	private $_scrabble_gateway;

	/**
	 * @Inject("@ScrabbleGateway")
	 */
	public function __construct(ScrabbleGateway $scrabble_gateway) {
		$this->_scrabble_gateway = $scrabble_gateway;
	}

	/**
	 * @param string[] $user_names
	 * @return GameVO $game
	 */
	public function addGame(array $user_names) {
		$game_vo = new GameVO();
		$game_vo->game_id = $this->generateRandomNumericId();
		$game_vo->user_count = count($user_names);
		$game_vo->user_names = implode(',', $user_names);
		$game_vo->created_at = time();
		$game_vo->current_user_idx = 0;
		$game_vo->round = 0;
		$game_vo->status = GameVO::STATUS_RUNNING;
		$game_vo->points_sum = 0;

		$this->_scrabble_gateway->addGame($game_vo);

		return $game_vo;
	}

	/**
	 * @param integer $game_id
	 * @param integer $points
	 */
	public function addPoints($game_id, $points) {
		$game_vo = $this->_scrabble_gateway->getGameVO($game_id);
		$current_user_idx = $game_vo->current_user_idx;

		$this->_scrabble_gateway->addPoints($game_id, $current_user_idx, $points);

		$next_user_id = ++$current_user_idx % $game_vo->user_count;
		if ($next_user_id === 0) {
			$this->_scrabble_gateway->setGameProperty($game_id, 'round', $game_vo->round + 1);
		}

		$this->_scrabble_gateway->setGameProperty($game_id, 'current_user_idx', $next_user_id);
	}

	/**
	 * @param integer $game_id
	 */
	public function finishGame($game_id) {
		$this->_scrabble_gateway->setGameProperty($game_id, 'status', GameVO::STATUS_STOPPED);
	}

	/**
	 * @param integer $game_id
	 * @return array[]
	 */
	public function getPoints($game_id) {
		return $this->_scrabble_gateway->getPoints($game_id);
	}

	/**
	 * @return GameVO[]
	 */
	public function getGames() {
		return $this->_scrabble_gateway->getGames();
	}

	/**
	 * @param integer $game_id
	 * @return GameVO
	 */
	public function getGameVO($game_id) {
		return $this->_scrabble_gateway->getGameVO($game_id);
	}

} 