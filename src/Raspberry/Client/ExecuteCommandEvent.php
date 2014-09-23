<?php

namespace Raspberry\Client;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class ExecuteCommandEvent extends AbstractEvent {

	const EXECUTE = 'command.execute';

	/**
	 * @var string
	 */
	public $command;

	/**
	 * @var boolean
	 */
	public $return_needed;

	/**
	 * @param string $command
	 * @param boolean $return_needed
	 */
	public function __construct($command, $return_needed) {
		$this->event_name = self::EXECUTE;
		$this->command = $command;
		$this->return_needed = $return_needed;
	}
} 