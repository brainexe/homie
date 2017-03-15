<?php

namespace Homie\Remote;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;

use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Remote\Event\ReceivedEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation
 */
class Controller
{

    use EventDispatcherTrait;

    /**
     * @param Request $request
     * @param string $code
     * @return bool
     * @Route("/remote/receive/{code}/", name="remote.receive")
     */
    public function action(Request $request, string $code) : bool
    {
        unset($request);

        $event = new ReceivedEvent($code);

        $this->dispatchEvent($event);

        return true;
    }
}
