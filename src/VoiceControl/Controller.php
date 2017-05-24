<?php

namespace Homie\VoiceControl;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Traits\EventDispatcherTrait;
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
     * Controller constructor.
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request $request
     * @return bool
     * @Route("/speech/", name="speech.text")
     */
    public function text(Request $request) : bool
    {
        $text = $request->get('text');

        $event = new VoiceEvent(trim($text));

        $this->dispatchEvent($event);

        return true;
    }
}
