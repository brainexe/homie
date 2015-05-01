<?php

namespace Raspberry\Espeak;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;

class EspeakEvent extends AbstractEvent implements PushViaWebsocket
{

    const SPEAK = 'espeak.speak';

    /**
     * @var EspeakVO
     */
    public $espeak;

    /**
     * @param EspeakVO $espeak
     */
    public function __construct(EspeakVO $espeak)
    {
        $this->event_name = self::SPEAK;
        $this->espeak     = $espeak;
    }
}
