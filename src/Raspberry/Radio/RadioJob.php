<?php

namespace Raspberry\Radio;

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
     * @param MessageQueueGateway $message_queue_gateway
     * @param TimeParser $time_parser
     */
    public function __construct(MessageQueueGateway $message_queue_gateway, TimeParser $time_parser)
    {
        $this->messageQueueGateway = $message_queue_gateway;
        $this->timeParser = $time_parser;
    }

    /**
     * @return MessageQueueJob[]
     */
    public function getJobs()
    {
        return $this->messageQueueGateway->getEventsByType(RadioChangeEvent::CHANGE_RADIO, time());
    }

    /**
     * @param RadioVO $radio_vo
     * @param string $time_string
     * @param boolean $status
     */
    public function addRadioJob(RadioVO $radio_vo, $time_string, $status)
    {
        $timestamp = $this->timeParser->parseString($time_string);

        $event = new RadioChangeEvent($radio_vo, $status);
        $this->dispatchInBackground($event, $timestamp);
    }

    /**
     * @param string $job_id
     */
    public function deleteJob($job_id)
    {
        $this->messageQueueGateway->deleteEvent($job_id, RadioChangeEvent::CHANGE_RADIO);
    }
}
