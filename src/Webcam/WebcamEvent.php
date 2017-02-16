<?php

namespace Homie\Webcam;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\JsonSerializableTrait;

class WebcamEvent extends AbstractEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;

    const TAKE_PHOTO = 'webcam.take_photo';
    const TAKE_VIDEO = 'webcam.take_video';
    const TAKE_SOUND = 'webcam.take_sound';

    const TOOK_PHOTO = 'webcam.took_photo';
    const TOOK_VIDEO = 'webcam.took_video';
    const TOOK_SOUND = 'webcam.took_sound';

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $duration;

    /**
     * @param string $name
     * @param string $eventType
     * @param int $duration
     */
    public function __construct(
        string $name,
        string $eventType,
        int    $duration = 0
    ) {
        parent::__construct($eventType);
        $this->name     = $name;
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDuration() : int
    {
        return $this->duration;
    }
}
