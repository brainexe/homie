<?php

namespace Homie\Client;

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
     * @var string[]
     */
    private $arguments;

    /**
     * @param string $command
     * @param string[] $arguments
     * @param bool $returnNeeded
     */
    public function __construct($command, array $arguments, $returnNeeded)
    {
        parent::__construct(self::EXECUTE);
        $this->command      = $command;
        $this->returnNeeded = $returnNeeded;
        $this->arguments    = $arguments;
    }
}
