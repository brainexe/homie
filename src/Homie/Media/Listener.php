<?php

namespace Homie\Media;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;

/**
 * @EventListener("Media.Listener")
 */
class Listener
{

    /**
     * @var Sound
     */
    private $sound;

    /**
     * @Inject("@Media.Sound")
     * @param Sound $recorder
     */
    public function __construct(Sound $recorder)
    {
        $this->sound = $recorder;
    }

    /**
     * @Listen(SoundEvent::PLAY_SOUND)
     * @param SoundEvent $event
     */
    public function handleEvent(SoundEvent $event)
    {
        $this->sound->playSound($event->getFileName());
    }
}
