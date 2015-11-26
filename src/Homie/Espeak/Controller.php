<?php

namespace Homie\Espeak;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use BrainExe\Core\MessageQueue\Job;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("EspeakController")
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
     * @Route("/espeak/", name="espeak.index")
     */
    public function index()
    {
        $speakers = $this->espeak->getSpeakers();
        $jobs     = $this->espeak->getPendingJobs(); // TODO MQ

        return [
            'speakers' => $speakers,
            'jobs' => $jobs
        ];
    }

    /**
     * @param Request $request
     * @return Job[]
     * @Route("/espeak/speak/", methods="POST", name="espeak.speak")
     */
    public function speak(Request $request)
    {
        $speaker   = $request->request->get('speaker') ?: null;
        $text      = $request->request->get('text');
        $volume    = $request->request->getInt('volume');
        $speed     = $request->request->getInt('speed');
        $delayRaw  = $request->request->get('delay');

        $timestamp = $this->timeParser->parseString($delayRaw);

        $espeakVo  = new EspeakVO($text, $volume, $speed, $speaker);
        $event     = new EspeakEvent($espeakVo);

        $this->dispatchInBackground($event, $timestamp);

        $pendingJobs = $this->espeak->getPendingJobs();

        return $pendingJobs;
    }
}
