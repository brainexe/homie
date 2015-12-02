<?php

namespace Homie\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("EggTimerController")
 */
class Controller
{

    /**
     * @var EggTimer
     */
    private $timer;

    /**
     * @Inject("@EggTimer")
     * @param EggTimer $timer
     */
    public function __construct(EggTimer $timer)
    {
        $this->timer = $timer;
    }

    /**
     * @param Request $request
     * @return true
     * @Route("/egg_timer/", name="egg_timer.add", methods="POST")
     */
    public function add(Request $request)
    {
        $text = $request->request->get('text');
        $time = $request->request->get('time');

        $this->timer->addNewJob($time, $text);

        return true;
    }
}
