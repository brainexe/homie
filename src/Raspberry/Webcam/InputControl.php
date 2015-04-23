<?php

namespace Raspberry\Webcam;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;

/**
 * @InputControlAnnotation(name="webcam")
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
            '/^webcam$/i' => 'takeShot'
        ];
    }

    /**
     * @param Event $event
     */
    public function takeShot(Event $event)
    {
        $name  = microtime(true);
        $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

        $this->dispatchInBackground($event);
    }
}
