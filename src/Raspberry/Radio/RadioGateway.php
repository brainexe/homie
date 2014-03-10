<?php

namespace Raspberry\Radio;

use Matze\Core\Traits\IdGeneratorTrait;
use Matze\Core\Traits\RedisTrait;
use Raspberry\Radio\VO\RadioVO;

/**
 * @codeCoverageIgnore
 * @Service(public=false)
 */
class RadioGateway {

	use RedisTrait;
	use IdGeneratorTrait;

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
	 * @param integer $radio_id
	 * @return string
	 */
	private function _getRadioKey($radio_id) {
		return sprintf(self::REDIS_RADIO, $radio_id);
	}

	/**
	 * @param RadioVO $radio_vo
	 * @return integer $radio_id
	 */
	public function addRadio(RadioVO $radio_vo) {
		$new_radio_id = $this->generateRandomId();

		$pipeline = $this->getPredis()->pipeline();

		$key = $this->_getRadioKey($new_radio_id);
		$pipeline->HMSET($key, [
			'id' => $new_radio_id,
			'name' => $radio_vo->name,
			'description' => $radio_vo->description,
			'pin' => $radio_vo->pin,
			'code' => $radio_vo->code,
		]);

		$this->getPredis()->SADD(self::REDIS_RADIO_IDS, $new_radio_id);

		$pipeline->execute();

		return $new_radio_id;
	}

	/**
	 * @param integer $radio_id
	 */
	public function deleteRadio($radio_id) {
		$predis = $this->getPredis();

		$predis->SREM(self::REDIS_RADIO_IDS, $radio_id);
		$predis->DEL(self::_getRadioKey($radio_id));
	}
}
