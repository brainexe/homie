<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\MessageQueue\Gateway;
use BrainExe\Core\MessageQueue\Job;
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
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject({"@MessageQueue.Gateway", "@TimeParser"})
     * @param Gateway $gateway
     * @param TimeParser $timeParser
     */
    public function __construct(Gateway $gateway, TimeParser $timeParser)
    {
        $this->gateway    = $gateway;
        $this->timeParser = $timeParser;
    }

    /**
     * @deprecated
     * @todo
     * @return Job[]
     */
    public function getJobs()
    {
        return $this->gateway->getEventsByType(RadioChangeEvent::CHANGE_RADIO, time());
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
