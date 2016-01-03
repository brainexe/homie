<?php

namespace Homie\Webcam;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;

class WebcamEvent extends AbstractEvent implements PushViaWebsocket
{
    const TAKE_PHOTO = 'webcam.take_photo';
    const TAKE_VIDEO = 'webcam.take_video';
    const TAKE_SOUND = 'webcam.take_sound';

    const TOOK_PHOTO = 'webcam.took_photo';
    const TOOK_VIDEO = 'webcam.took_video';
    const TOOK_SOUND = 'webcam.took_sound';

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $duration;

    /**
     * @param string $eventName
     * @param string $eventType self::
     * @param int $duration
     */
    public function __construct($eventName, $eventType, $duration = 0)
    {
        parent::__construct($eventType);
        $this->name     = $eventName;
        $this->duration = $duration;
    }
}
