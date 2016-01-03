<?php

namespace Homie\IFTTT;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;

/**
 * @InputControlAnnotation("IFTTT.InputControl.")
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
            '/^ifttt trigger (.*)$/i' => 'trigger'
        ];
    }

    /**
     * @param Event $event
     */
    public function trigger(Event $event)
    {
        $event = new IFTTTEvent(IFTTTEvent::TRIGGER, $event->match);

        $this->dispatchEvent($event);
    }
}
