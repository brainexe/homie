<?php

namespace Homie\IFTTT;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation("IFTTT.Controller")
 */
class Controller
{

    use EventDispatcherTrait;

    /**
     * @return Response
     * @Route("/ifttt/", name="ifttt.trigger")
     * @Guest
     */
    public function index(Request $request)
    {
        $eventName = $request->query->get('event');

        $event = new IFTTTEvent($eventName);

        $this->dispatchEvent($event);
    }
}
