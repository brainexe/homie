<?php

namespace Raspberry\Client;

interface ClientInterface {
	/**
	 * @param string $command
	 */
	public function execute($command);
}