<?php

namespace Raspberry\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Traits\TimeTrait;
use BrainExe\MessageQueue\Gateway;
use BrainExe\MessageQueue\Job;
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
     * @var Gateway
     */
    private $gateway;

    /**
     * @var TimeParser
     */
    private $timeParser;

    /**
     * @Inject({"@MessageQueue.Gateway", "@TimeParser"})
     * @param Gateway $gateway
     * @param TimeParser $timeParser
     */
    public function __construct(
        Gateway $gateway,
        TimeParser $timeParser
    ) {
        $this->gateway     = $gateway;
        $this->timeParser  = $timeParser;
    }

    /**
     * @param string $time
     * @param string $text
     * @throws UserException
     */
    public function addNewJob($time, $text)
    {
        $espeakVo = null;

        if ($text) {
            $espeakVo = new EspeakVO($text);
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
        $this->gateway->deleteEvent(
            $jobId,
            EggTimerEvent::DONE
        );
    }

    /**
     * @return Job[]
     */
    public function getJobs()
    {
        return $this->gateway->getEventsByType(
            EggTimerEvent::DONE,
            $this->now()
        );
    }
}
