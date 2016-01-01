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
    public $fileName;

    /**
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        parent::__construct(self::PLAY_SOUND);
        $this->fileName     = $fileName;
    }
}
