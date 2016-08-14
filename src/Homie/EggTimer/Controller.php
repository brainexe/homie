<?php

namespace Homie\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
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
     * @Inject("@EggTimer")
     * @param EggTimer $timer
     */
    public function __construct(EggTimer $timer)
    {
        $this->timer = $timer;
    }

    /**
     * @param Request $request
     * @return bool
     * @Route("/egg_timer/", name="egg_timer.add", methods="POST")
     */
    public function add(Request $request) : bool
    {
        $text = $request->request->get('text', '');
        $time = $request->request->get('time', '');

        $this->timer->addNewJob($time, $text);

        return true;
    }
}
