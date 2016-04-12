<?php

namespace Homie\Motion;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;

/**
 * @ControllerAnnotation("Motion.Controller")
 */
class Controller
{

    use EventDispatcherTrait;

    /**
     * @return bool
     * @Route("/motion/add/", name="motion.add")
     * @Guest
     */
    public function add() : bool
    {
        $event = new MotionEvent(MotionEvent::MOTION);

        $this->dispatchEvent($event);

        return true;
    }
}
