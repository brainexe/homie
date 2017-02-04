<?php

namespace Homie\EggTimer;


use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\MessageQueue\Job;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("EggTimer.Controller")
 */
class Controller
{

    /**
     * @var EggTimer
     */
    private $timer;

    /**
     * @param EggTimer $timer
     */
    public function __construct(EggTimer $timer)
    {
        $this->timer = $timer;
    }

    /**
     * @param Request $request
     * @return Job
     * @Route("/egg_timer/", name="egg_timer.add", methods="POST")
     */
    public function add(Request $request) : Job
    {
        $text = $request->request->get('text', '');
        $time = $request->request->get('time', '');

        return $this->timer->addNewJob($time, $text);
    }
}
