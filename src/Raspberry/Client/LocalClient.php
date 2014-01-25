<?php

namespace Raspberry\Client;

use RuntimeException;
use Symfony\Component\Process\Process;

class LocalClient {

	/**
	 * {@inheritdoc}
	 */
	public function execute($command) {
		$process = new Process($command);
		$process->setTimeout(3600);
		$process->run();

		if (!$process->isSuccessful()) {
			throw new RuntimeException($process->getErrorOutput());
		}

		return $process->getOutput();
	}
}