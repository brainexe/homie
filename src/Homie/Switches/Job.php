<?php

namespace Homie\Switches;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Job", public=false)
 */
class Job
{

    use EventDispatcherTrait;

    /**
     * @var TimeParser
     */
    private $timeParser;

    /**
     * @Inject({"@TimeParser"})
     * @param TimeParser $timeParser
     */
    public function __construct(TimeParser $timeParser)
    {
        $this->timeParser = $timeParser;
    }

    /**
     * @param SwitchVO $switch
     * @param string $timeString
     * @param boolean $status
     */
    public function addJob(SwitchVO $switch, $timeString, $status)
    {
        $timestamp = $this->timeParser->parseString($timeString);

        $event = new SwitchChangeEvent($switch, $status);
        $this->dispatchInBackground($event, $timestamp);
    }
}
