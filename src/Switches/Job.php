<?php

namespace Homie\Switches;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\TimeParser;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service
 */
class Job
{

    /**
     * @var TimeParser
     */
    private $timeParser;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @param TimeParser $timeParser
     * @param EventDispatcher $dispatcher
     */
    public function __construct(
        TimeParser $timeParser,
        EventDispatcher $dispatcher
    ) {
        $this->timeParser = $timeParser;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param SwitchVO $switch
     * @param string $timeString
     * @param bool $status
     */
    public function addJob(SwitchVO $switch, string $timeString, bool $status) : void
    {
        $timestamp = $this->timeParser->parseString($timeString);

        $event = new SwitchChangeEvent($switch, $status);
        $this->dispatcher->dispatchInBackground($event, $timestamp);
    }
}
