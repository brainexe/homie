<?php

namespace Homie\Webcam;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;

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

    public function takeShot()
    {
        $name  = microtime(true);
        $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

        $this->dispatchInBackground($event);
    }
}
