<?php

namespace Matze\Core\Traits;

use Predis\Client;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

trait RedisCacheTrait {

	/**
	 * @var Client
	 */
	private $_predis;

	/**
	 * @DI\Inject("@Predis")
	 */
	public function setPredis(Client $client) {
		$this->_predis = $client;
	}

	/**
	 * @param string $key
	 * @param callable $callback
	 * @param integer $ttl
	 * @return mixed
	 */
	protected function wrapCache($key, $callback, $ttl = 3600) {
		$cached_value = $this->_predis->GET($key);
		if ($cached_value) {
			return unserialize($cached_value);
		}

		$value = $callback();

		$this->_predis->SET($key, serialize($value));
		$this->_predis->EXPIRE($key, $ttl);

		return $value;
	}

	protected function invalidate($key) {
		$this->_predis->DEL($key);
	}
}