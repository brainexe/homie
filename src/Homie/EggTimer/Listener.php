<?php

namespace Homie\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Espeak\EspeakEvent;
use Homie\Media\Sound;

/**
 * @EventListener("EggTimer.Listener")
 */
class Listener
{

    use EventDispatcherTrait;

    /**
     * @var Sound
     */
    private $sound;

    /**
     * @Inject("@Sound")
     * @param Sound $sound
     */
    public function __construct(Sound $sound)
    {
        $this->sound = $sound;
    }

    /**
     * @Listen(EggTimerEvent::DONE)
     * @param EggTimerEvent $event
     */
    public function handleEggTimerEvent(EggTimerEvent $event)
    {
        $this->sound->playSound(EggTimer::EGG_TIMER_RING_SOUND);

        if ($event->espeak) {
            $newEvent = new EspeakEvent($event->espeak);
            $this->dispatchEvent($newEvent);
        }
    }
}
