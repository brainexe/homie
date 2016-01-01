<?php

namespace Homie\Media;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;

/**
 * @InputControlAnnotation("Sound.InputControl")
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
            '/^play sound (.*)$/i' => 'play'
        ];
    }

    /**
     * @param Event $event
     */
    public function play(Event $event)
    {
        $soundEvent = new SoundEvent($event->match);
        $this->dispatchEvent($soundEvent);
    }
}
