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
     * @var bool
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
    public function __construct(string $command, array $arguments, bool $returnNeeded = false)
    {
        parent::__construct(self::EXECUTE);

        $this->command      = $command;
        $this->arguments    = $arguments;
        $this->returnNeeded = $returnNeeded;
    }

    /**
     * @return string
     */
    public function getCommand() : string
    {
        return $this->command;
    }

    /**
     * @return string[]
     */
    public function getArguments() : array
    {
        return $this->arguments;
    }

    /**
     * @return bool
     */
    public function isReturnNeeded() : bool
    {
        return $this->returnNeeded;
    }
}
