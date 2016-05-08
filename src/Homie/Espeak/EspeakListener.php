<?php

namespace Homie\Espeak;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;

/**
 * @EventListener
 */
class EspeakListener
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
     * @Listen(EspeakEvent::SPEAK)
     * @param EspeakEvent $event
     */
    public function handleEspeakEvent(EspeakEvent $event)
    {
        $espeakVo = $event->getEspeak();

        $this->espeak->speak(
            $espeakVo->text,
            $espeakVo->volume ?: 100,
            $espeakVo->speed  ?: 100,
            $espeakVo->speaker
        );
    }
}
