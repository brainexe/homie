<?php

namespace Homie\Motion;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;

/**
 * @ControllerAnnotation
 */
class Controller
{

    use EventDispatcherTrait;

    /**
     * @return bool
     * @Route("/motion/add/", name="motion.add")
     */
    public function add() : bool
    {
        $event = new MotionEvent(MotionEvent::MOTION);

        $this->dispatchEvent($event);

        return true;
    }
}
