<?php

namespace Raspberry\Client;

use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * @Service("RaspberryClient.Local")
 */
class LocalClient implements ClientInterface {

	/**
	 * {@inheritdoc}
	 */
	public function execute($command) {
		$this->execute($command);
	}

	/**
	 * {@inheritdoc}
	 */
	public function executeWithReturn($command) {
		$process = new Process($command);
		$process->setTimeout(3600);
		$process->run();

		if (!$process->isSuccessful()) {
			throw new RuntimeException($process->getErrorOutput());
		}

		return $process->getOutput();
	}
}