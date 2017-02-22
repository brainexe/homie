<?php

namespace Homie\Espeak\Controller;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\MessageQueue\Job;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Espeak.Controller.Speak")
 */
class Speak
{

    use EventDispatcherTrait;

    /**
     * @var TimeParser
     */
    private $timeParser;

    /**
     * @param TimeParser $timeParser
     */
    public function __construct(TimeParser $timeParser)
    {
        $this->timeParser = $timeParser;
    }

    /**
     * @param Request $request
     * @return Job
     * @Route("/espeak/speak/", methods="POST", name="espeak.speak")
     */
    public function speak(Request $request) : Job
    {
        $speaker   = (string)$request->request->get('speaker');
        $text      = (string)$request->request->get('text');
        $volume    = $request->request->getInt('volume');
        $speed     = $request->request->getInt('speed');
        $delayRaw  = (string)$request->request->get('delay');

        $timestamp = $this->timeParser->parseString($delayRaw);

        $espeakVo  = new EspeakVO($text, $volume, $speed, $speaker);
        $event     = new EspeakEvent($espeakVo);

        return $this->dispatchInBackground($event, $timestamp);
    }
}
