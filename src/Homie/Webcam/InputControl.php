<?php

namespace Homie\Webcam;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;
use BrainExe\InputControl\Event;

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
            '/^webcam$/i' => 'takeShot',
            '/^webcam video (\d+) seconds$/i' => 'takeVideo'
        ];
    }

    public function takeShot()
    {
        $name  = microtime(true);
        $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

        $this->dispatchInBackground($event);
    }

    public function takeVideo(Event $event)
    {
        $name  = microtime(true);
        $event = new WebcamEvent($name, WebcamEvent::TAKE_VIDEO, $event->match);

        $this->dispatchInBackground($event);
    }
}
