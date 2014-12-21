<?php

namespace Raspberry\Client;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class ExecuteCommandEvent extends AbstractEvent
{

    const EXECUTE = 'command.execute';

    /**
     * @var string
     */
    public $command;

    /**
     * @var boolean
     */
    public $returnNeeded;

    /**
     * @param string $command
     * @param boolean $returnNeeded
     */
    public function __construct($command, $returnNeeded)
    {
        $this->event_name   = self::EXECUTE;
        $this->command      = $command;
        $this->returnNeeded = $returnNeeded;
    }
}
