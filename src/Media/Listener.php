<?php

namespace Homie\Media;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;

/**
 * @EventListener
 */
class Listener
{

    /**
     * @var Sound
     */
    private $sound;

    /**
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
    public function handleEvent(SoundEvent $event): void
    {
        $this->sound->playSound($event->getFileName());
    }
}
