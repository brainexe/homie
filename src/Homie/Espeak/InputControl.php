<?php

namespace Homie\Espeak;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;

/**
 * @InputControlAnnotation("InputControl.espeak")
 */
class InputControl implements InputControlInterface
{

    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            '/^(say|speak) (.*)$/' => 'say'
        ];
    }

    /**
     * @param Event $event
     */
    public function say(Event $event)
    {
        $event = new EspeakEvent(new EspeakVO($event->matches[1]));

        $this->dispatchEvent($event);
    }
}
