<?php

namespace Raspberry\Scrabble;

use Matze\Core\Traits\RedisTrait;
use Raspberry\Scrabble\VO\GameVO;
use Redis;

/**
 * @Service(public=false)
 */
class ScrabbleGateway {

	const KEY_GAMES = 'scrabble:games';
	const KEY_GAME = 'scrabble:game:%d';
	const KEY_POINTS = 'scrabble:points:%d:%d';

	use RedisTrait;


	/**
	 * @param GameVO $game
	 */
	public function addGame(GameVO $game) {
		$redis = $this->getRedis();

		$game_key = $this->_getKeyGame($game->game_id);
		$redis->hMset($game_key, (array)$game);

		$redis->sAdd(self::KEY_GAMES, $game->game_id);
	}

	/**
	 * @param integer $game_id
	 * @param integer $user_idx
	 * @param integer $points
	 */
	public function addPoints($game_id, $user_idx, $points) {
		$redis = $this->getRedis();

		$points_key = $this->_getKeyPoints($game_id, $user_idx);
		$redis->lPush($points_key, $points);

		$game_key = $this->_getKeyGame($game_id);
		$redis->hIncrBy($game_key, 'points_sum', $points);
	}

	/**
	 * @param integer $game_id
	 * @return GameVO
	 */
	public function getGameVO($game_id) {
		$game_key = $this->_getKeyGame($game_id);
		$game_raw = $this->getRedis()->hGetAll($game_key);

		$game_vo = new GameVO();
		$game_vo->fillValues($game_raw);

		return $game_vo;
	}

	/**
	 * @param integer $game_id
	 * @param string $property
	 * @param mixed $value
	 */
	public function setGameProperty($game_id, $property, $value) {
		$game_key = $this->_getKeyGame($game_id);
		$this->getRedis()->hSet($game_key, $property, $value);
	}

	/**
	 * @param integer $game_id
	 * @return array[]
	 */
	public function getPoints($game_id) {
		$redis = $this->getRedis();

		$game_key = $this->_getKeyGame($game_id);
		$user_count = $redis->hGet($game_key, 'user_count');

		$result = [];
		for ($i = 0; $i < $user_count; $i++) {
			$points_key = $this->_getKeyPoints($game_id, $i);
			$result[$i] = $redis->lRange($points_key, -0, 100);
		}

		return $result;
	}

	/**
	 * @return GameVO[]
	 */
	public function getGames() {
		$redis = $this->getRedis();

		$game_ids = $redis->sMembers(self::KEY_GAMES);

		$pipeline = $redis->multi(Redis::PIPELINE);
		foreach ($game_ids as $game_id) {
			$pipeline->hGetAll($this->_getKeyGame($game_id));
		}
		$games_raw = $pipeline->exec();

		$result = [];
		foreach ($games_raw as $game_raw) {
			$game_vo = new GameVO();
			$game_vo->fillValues($game_raw);

			$result[$game_vo->game_id] = $game_vo;
		}

		return $result;
	}

	/**
	 * @param integer $game_id
	 * @return string
	 */
	public function _getKeyGame($game_id) {
		return sprintf(self::KEY_GAME, $game_id);
	}

	/**
	 * @param integer $game_id
	 * @param integer $user_idx
	 * @return string
	 */
	public function _getKeyPoints($game_id, $user_idx) {
		return sprintf(self::KEY_POINTS, $game_id, $user_idx);
	}

} 