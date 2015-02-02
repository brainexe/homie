<?php

namespace Raspberry\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Traits\TimeTrait;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\MessageQueue\MessageQueueJob;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use Raspberry\Espeak\EspeakVO;

/**
 * @Service(public=false)
 */
class EggTimer
{

    const EGG_TIMER_RING_SOUND = 'assets/sounds/egg_timer.mp3';

    use TimeTrait;
    use EventDispatcherTrait;

    /**
     * @var MessageQueueGateway
     */
    private $messageQueueGateway;

    /**
     * @var TimeParser
     */
    private $timeParser;

    /**
     * @Inject({"@MessageQueueGateway", "@TimeParser"})
     * @param MessageQueueGateway $messageQueueGateway
     * @param TimeParser $timeParser
     */
    public function __construct(
        MessageQueueGateway $messageQueueGateway,
        TimeParser $timeParser
    ) {
        $this->messageQueueGateway = $messageQueueGateway;
        $this->timeParser          = $timeParser;
    }

    /**
     * @param string $time
     * @param string $text
     * @throws UserException
     */
    public function addNewJob($time, $text)
    {
        if ($text) {
            $espeakVo = new EspeakVO($text);
        } else {
            $espeakVo = null;
        }

        $event = new EggTimerEvent($espeakVo);

        $timestamp = $this->timeParser->parseString($time);

        $this->dispatchInBackground($event, $timestamp);
    }

    /**
     * @param string $jobId
     */
    public function deleteJob($jobId)
    {
        $this->messageQueueGateway->deleteEvent($jobId, EggTimerEvent::DONE);
    }

    /**
     * @return MessageQueueJob[]
     */
    public function getJobs()
    {
        return $this->messageQueueGateway->getEventsByType(
            EggTimerEvent::DONE,
            $this->now()
        );
    }
}
