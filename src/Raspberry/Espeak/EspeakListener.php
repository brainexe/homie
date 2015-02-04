<?php

namespace Raspberry\Espeak;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class EspeakListener implements EventSubscriberInterface
{

    /**
     * @var Espeak
     */
    private $espeak;

    /**
     * @Inject("@espeak")
     * @param Espeak $espeak
     */
    public function __construct(Espeak $espeak)
    {
        $this->espeak = $espeak;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            EspeakEvent::SPEAK => 'handleEspeakEvent'
        ];
    }

    /**
     * @param EspeakEvent $event
     */
    public function handleEspeakEvent(EspeakEvent $event)
    {
        $espeakVo = $event->espeak;

        $this->espeak->speak(
            $espeakVo->text,
            $espeakVo->volume ?: 100,
            $espeakVo->speed ?: 100,
            $espeakVo->speaker ?: Espeak::DEFAULT_SPEAKER
        );
    }
}
