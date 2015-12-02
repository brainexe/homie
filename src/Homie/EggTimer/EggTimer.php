<?php

namespace Homie\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use Homie\Espeak\EspeakVO;

/**
 * @Service(public=false)
 */
class EggTimer
{

    const EGG_TIMER_RING_SOUND = 'assets/sounds/egg_timer.mp3';

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
}
