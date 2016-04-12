<?php

namespace Homie\Media;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("Media.Listener")
 */
class Listener implements EventSubscriberInterface
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SoundEvent::PLAY_SOUND => 'handleEvent',
        ];
    }

    /**
     * @param SoundEvent $event
     */
    public function handleEvent(SoundEvent $event)
    {
        $this->sound->playSound($event->getFileName());
    }
}
