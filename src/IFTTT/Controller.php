<?php

namespace Homie\IFTTT;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\IFTTT\Event\ActionEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation
 */
class Controller
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request $request
     * @return bool
     * @Route("/ifttt/", name="ifttt.action")
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

        $this->dispatcher->dispatchEvent($event);

        return true;
    }
}
