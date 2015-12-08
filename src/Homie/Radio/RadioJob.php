<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use Homie\Radio\VO\RadioVO;

/**
 * @Service(public=false)
 */
class RadioJob
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
     * @param RadioVO $radio
     * @param string $timeString
     * @param boolean $status
     */
    public function addRadioJob(RadioVO $radio, $timeString, $status)
    {
        $timestamp = $this->timeParser->parseString($timeString);

        $event = new RadioChangeEvent($radio, $status);
        $this->dispatchInBackground($event, $timestamp);
    }
}
