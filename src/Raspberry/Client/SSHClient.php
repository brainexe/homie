<?php

namespace Raspberry\Client;

/**
 * @Service("RaspberryClient.SSH", public=false)
 */
class SSHClient implements ClientInterface {

	/**
	 * {@inheritdoc}
	 */
	public function execute($command) {
		throw new \Exception("SSH client is not implemented");
	}

	/**
	 * @param string $command
	 * @return string
	 */
	public function executeWithReturn($command) {
		throw new \Exception("SSH client is not implemented");

	}
}