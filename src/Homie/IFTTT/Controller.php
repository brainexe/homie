<?php

namespace Homie\IFTTT;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\IFTTT\Event\ActionEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("IFTTT.Controller")
 */
class Controller
{

    use EventDispatcherTrait;

    /**
     * @param Request $request
     * @return bool
     * @Route("/ifttt/", name="ifttt.action")
     * @Guest
     */
    public function action(Request $request) : bool
    {
        $eventName = $request->query->get('event');
        $value1    = $request->query->get('value1');
        $value2    = $request->query->get('value2');
        $value3    = $request->query->get('value3');

        $event = new ActionEvent(
            $eventName,
            $value1,
            $value2,
            $value3
        );

        $this->dispatchEvent($event);

        return true;
    }
}
