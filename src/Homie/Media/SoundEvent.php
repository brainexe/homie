<?php

namespace Homie\Media;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;

class SoundEvent extends AbstractEvent implements PushViaWebsocket
{
    const PLAY_SOUND = 'sound.play';

    /**
     * @var string
     */
    private $fileName;

    /**
     * @param string $eventName
     */
    public function __construct(string $eventName)
    {
        parent::__construct(self::PLAY_SOUND);
        $this->fileName = $eventName;
    }

    /**
     * @return string
     */
    public function getFileName() : string
    {
        return $this->fileName;
    }
}
