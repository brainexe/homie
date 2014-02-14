<?php

namespace Raspberry\Client;

use RuntimeException;
use Symfony\Component\Process\Process;
use Matze\Annotations\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
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