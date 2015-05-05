<?php

namespace Homie\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Espeak\EspeakEvent;
use Homie\Media\Sound;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class EggTimerListener implements EventSubscriberInterface
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            EggTimerEvent::DONE => 'handleEggTimerEvent'
        ];
    }

    /**
     * @param EggTimerEvent $event
     */
    public function handleEggTimerEvent(EggTimerEvent $event)
    {
        $this->sound->playSound(ROOT . EggTimer::EGG_TIMER_RING_SOUND);

        if ($event->espeak) {
            $newEvent = new EspeakEvent($event->espeak);

            $this->dispatchEvent($newEvent);
        }
    }
}
