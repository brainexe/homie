<?php

namespace Raspberry\Gpio;

use Matze\Core\Traits\RedisTrait;

/**
 * @codeCoverageIgnore
 * @Service(public=false)
 */
class PinGateway {
	const REDIS_PINS = 'pins';

	use RedisTrait;

	/**
	 * @return array[]
	 */
	public function getPinDescriptions() {
		$predis = $this->getPredis();

		return $predis->HGETALL(self::REDIS_PINS);
	}

} 