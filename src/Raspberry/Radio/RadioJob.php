<?php

namespace Raspberry\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\MessageQueue\MessageQueueJob;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use Raspberry\Radio\VO\RadioVO;

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
     * @var MessageQueueGateway
     */
    private $messageQueueGateway;

    /**
     * @Inject({"@MessageQueueGateway", "@TimeParser"})
     * @param MessageQueueGateway $messageQueueGateway
     * @param TimeParser $timeParser
     */
    public function __construct(MessageQueueGateway $messageQueueGateway, TimeParser $timeParser)
    {
        $this->messageQueueGateway = $messageQueueGateway;
        $this->timeParser = $timeParser;
    }

    /**
     * @return MessageQueueJob[]
     */
    public function getJobs()
    {
        return $this->messageQueueGateway->getEventsByType(RadioChangeEvent::CHANGE_RADIO, time());
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

    /**
     * @param string $jobId
     */
    public function deleteJob($jobId)
    {
        $this->messageQueueGateway->deleteEvent($jobId, RadioChangeEvent::CHANGE_RADIO);
    }
}
