<?php

namespace Raspberry\Radio;

use Raspberry\Client\LocalClient;

/**
 * @Service
 */
class RadioController {
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
		$command = sprintf('%s %s %d %d', self::BASE_COMMAND, $code, $number, (int)$status);
		$this->_local_client->execute($command);
	}
} 