<?php

namespace Raspberry\Client;

use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * @Service("RaspberryClient.Local", public=false)
 */
class LocalClient implements ClientInterface {

	/**
	 * {@inheritdoc}
	 */
	public function execute($command) {
		$this->executeWithReturn($command);
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