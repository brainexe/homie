<?php

namespace Raspberry\Client;

interface ClientInterface {

	/**
	 * @param string $command
	 */
	public function execute($command);

	/**
	 * @param string $command
	 * @return string
	 */
	public function executeWithReturn($command);
}