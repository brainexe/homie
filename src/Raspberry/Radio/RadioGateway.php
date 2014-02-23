<?php

namespace Raspberry\Radio;

use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class RadioGateway {

	use RedisTrait;

	const REDIS_RADIO = 'radios:%d';
	const REDIS_RADIO_IDS = 'radio_ids';

	/**
	 * @return array[]
	 */
	public function getRadios() {
		$radio_ids = $this->getRadioIds();

		$pipeline = $this->getPredis()->pipeline();

		foreach ($radio_ids as $radio_id) {
			$pipeline->HGETALL(self::_getRadioKey($radio_id));
		}

		return $pipeline->execute();
	}

	/**
	 * @param integer $radio_id
	 * @return array
	 */
	public function getRadio($radio_id) {
		return $this->getPredis()->HGETALL($this->_getRadioKey($radio_id));
	}

	/**
	 * @return integer[]
	 */
	public function getRadioIds() {
		$radio_ids = $this->getPredis()->SMEMBERS(self::REDIS_RADIO_IDS);

		sort($radio_ids);

		return $radio_ids;
	}

	/**
	 * @param string $name
	 * @param string $description
	 * @param string $pin
	 * @param string $code
	 */
	public function addRadio($name, $description, $pin, $code) {
		$radio_ids = $this->getRadioIds();
		$new_radio_id = end($radio_ids) + 1;

		$pipeline = $this->getPredis()->pipeline();

		$key = $this->_getRadioKey($new_radio_id);
		$pipeline->HMSET($key, [
			'id' => $new_radio_id,
			'name' => $name,
			'description' => $description,
			'pin' => $pin,
			'code' => $code,
		]);

		$this->getPredis()->SADD(self::REDIS_RADIO_IDS, $new_radio_id);

		$pipeline->execute();
	}

	/**
	 * @param integer $radio_id
	 * @return string
	 */
	private function _getRadioKey($radio_id) {
		return sprintf(self::REDIS_RADIO, $radio_id);
	}
} 