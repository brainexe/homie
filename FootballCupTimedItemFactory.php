<?php

namespace Ig\StratCity\Classes\Models\SeasonalEvent\FootballCup\Item;

use Ig\StratCity\Classes\Models\Rewards\Vo\TimedRewardWrapperVO;
use Ig\StratCity\Classes\Models\SeasonalEvent\FootballCup\FootballEventTimer;
use Ig\StratCity\Classes\Models\SeasonalEvent\FootballCup\Reward\FootballCupTimedRewardLoader;
use Ig\StratCity\Classes\Models\SeasonalEvent\FootballCup\Vo\FootballCupSeasonalResourceVO;
use Ig\StratCity\Classes\Models\SeasonalEvent\FootballCup\Vo\Item\FootballCupTimedItemVO;

/**
 * @Service("Models.FootballCupTimedItemFactory")
 */
class FootballCupTimedItemFactory extends AbstractFootballCupItemFactory {

	use TimeTrait;

	const REWARDS_TO_LOAD = 4;

	/**
	 * @var FootballCupTimedRewardLoader
	 */
	private $_model_football_cup_timed_reward_loader;

	/**
	 * @var FootballEventTimer
	 */
	private $_model_football_event_timer;

	/**
	 * @Inject("Models.FootballEventTimer")
	 */
	public function setFootballEventTimer(FootballEventTimer $model) {
		$this->_model_football_event_timer = $model;
	}

	/**
	 * @Inject("Models.FootballCupTimedRewardLoader")
	 */
	public function setFootballCupTimedRewardLoader(FootballCupTimedRewardLoader $model) {
		$this->_model_football_cup_timed_reward_loader = $model;
	}

	/**
	 * @param string $era
	 * @return FootballCupTimedItemVO
	 */
	public function buildItem($era) {
		$item_vo = new FootballCupTimedItemVO();
		$item_vo->id = FootballCupTimedItemVO::REWARD_ID;
		$item_vo->is_available = true;

		$item_vo->rewards = $this->_getCurrentTimedRewards($era);

		if ($item_vo->rewards) {
			// set price of current rewards as item price
			$item_vo->price = new FootballCupSeasonalResourceVO();
			$item_vo->price->cups = $item_vo->rewards[0]->price;
		}

		return $item_vo;
	}

	/**
	 * @param string $era
	 * @return TimedRewardWrapperVO[]
	 */
	private function _getCurrentTimedRewards($era) {
		$rewards = [];

		$event_day = $this->_model_football_event_timer->getCurrentDay();
		$last_ending_at = strtotime("today"); // last item expired at 00:00
		$now = $this->getTime()->now();

		do {
			$reward_ids = $this->_model_football_cup_timed_reward_loader->getRewardIdsForEventDay($event_day);
			if (empty($reward_ids)) {
				// event ended -> no items left
				break;
			}

			$seconds_per_reward = 86400 / count($reward_ids);

			foreach ($reward_ids as $reward_id_per_day) {
				$last_ending_at += $seconds_per_reward; // add expire time for current item

				if ($last_ending_at < $now) {
					// skip expired items
					continue;
				}

				$reward_vo = $this->_model_football_cup_reward_builder->getReward($reward_id_per_day, $era);

				$timed_reward_wrapper = new TimedRewardWrapperVO();
				$timed_reward_wrapper->ending_at = $last_ending_at
				$timed_reward_wrapper->reward = $reward_vo;
//				$timed_reward_wrapper->price = new FootballCupSeasonalResourceVO(); // TODO add price here
//				$timed_reward_wrapper->cups = 10;

				$rewards[] = $timed_reward_wrapper;

				if (count($rewards) >= self::REWARDS_TO_LOAD) {
					break 2;
				}
			}

			$event_day++;
		} while (count($rewards) < self::REWARDS_TO_LOAD);

		return $rewards;
	}

}
