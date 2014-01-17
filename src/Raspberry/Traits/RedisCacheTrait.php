<?php

namespace Raspberry\Traits;

use Predis\Client;

trait RedisCacheTrait {

	/**
	 * @var Client
	 */
	private $_predis;

	/**
	 * @param Client $client
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

		$this->_predis->SET($key, serialize($value), 'EX', $ttl);

		return $value;
	}

	protected function invalidate($key) {
		$this->_predis->DEL($key);
	}
}