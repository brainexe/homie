<?php

namespace Raspberry\Radio;

use Raspberry\Client\LocalClient;

/**
 * @Service
 */
class RadioController {
	const STATUS_ENABLED = 'enabled';
	const STATUS_DISABLED = 'disabled';
	const STATUS_UNKNOWN = 'unknown';

	const BASE_COMMAND = 'sudo /opt/rcswitch-pi/send';

	/**
	 * @var LocalClient
	 */
	private $_local_client;

	/**
	 * @Inject("@LocalClient")
	 */
	public function __construct(LocalClient $local_client) {
		$this->_local_client = $local_client;
	}

	/**
	 * @param string $code
	 * @param integer $number
	 * @param boolean $status
	 */
	public function setStatus($code, $number, $status) {
		switch ($status) {
			case self::STATUS_ENABLED:
			case true:
				$status = true;
				break;

			case self::STATUS_DISABLED:
			default:
				$status = false;
				break;
		}
		$command = sprintf('%s %s %d %d', self::BASE_COMMAND, $code, $number, (int)$status);
		$this->_local_client->execute($command);
	}
} 