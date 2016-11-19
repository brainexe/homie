<?php

namespace Homie\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\MessageQueue\Job;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use Homie\Espeak\EspeakVO;

/**
 * @Service("EggTimer", public=false)
 */
class EggTimer
{

    const EGG_TIMER_RING_SOUND = 'egg_timer.mp3';

    use EventDispatcherTrait;

    /**
     * @var TimeParser
     */
    private $timeParser;

    /**
     * @Inject({"@TimeParser"})
     * @param TimeParser $timeParser
     */
    public function __construct(
        TimeParser $timeParser
    ) {
        $this->timeParser = $timeParser;
    }

    /**
     * @param string $time
     * @param string $text
     * @return Job
     */
    public function addNewJob(string $time, string $text) : Job
    {
        $espeakVo = null;
        if ($text) {
            $espeakVo = new EspeakVO($text);
        }

        $event = new EggTimerEvent($espeakVo);
        $timestamp = $this->timeParser->parseString($time);

        return $this->dispatchInBackground($event, $timestamp);
    }
}
