<?php

namespace Homie\EggTimer;

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

        $espeak = $event->getEspeak();
        if ($espeak) {
            $newEvent = new EspeakEvent($espeak);
            $this->dispatchEvent($newEvent);
        }
    }
}
