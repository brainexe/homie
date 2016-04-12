<?php

namespace Homie\Espeak;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Espeak.Controller")
 */
class Controller
{

    use EventDispatcherTrait;

    /**
     * @var Espeak
     */
    private $espeak;

    /**
     * @var TimeParser
     */
    private $timeParser;

    /**
     * @Inject({"@Espeak", "@TimeParser"})
     * @param Espeak $espeak
     * @param TimeParser $timeParser
     */
    public function __construct(Espeak $espeak, TimeParser $timeParser)
    {
        $this->espeak      = $espeak;
        $this->timeParser  = $timeParser;
    }

    /**
     * @return array
     * @Route("/espeak/speakers/", name="espeak.speakers")
     */
    public function speakers()
    {
        return [
            'speakers' => $this->espeak->getSpeakers(),
        ];
    }

    /**
     * @param Request $request
     * @return bool
     * @Route("/espeak/speak/", methods="POST", name="espeak.speak")
     */
    public function speak(Request $request) : bool
    {
        $speaker   = $request->request->get('speaker');
        $text      = $request->request->get('text');
        $volume    = $request->request->getInt('volume');
        $speed     = $request->request->getInt('speed');
        $delayRaw  = $request->request->get('delay');

        $timestamp = $this->timeParser->parseString($delayRaw);

        $espeakVo  = new EspeakVO($text, $volume, $speed, $speaker);
        $event     = new EspeakEvent($espeakVo);

        $this->dispatchInBackground($event, $timestamp);

        return true;
    }
}
