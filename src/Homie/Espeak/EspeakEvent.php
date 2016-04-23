<?php

namespace Homie\Espeak;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\SerializableTrait;

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
        parent::__construct(self::SPEAK);

        $this->espeak = $espeak;
    }

    /**
     * @return EspeakVO
     */
    public function getEspeak() : EspeakVO
    {
        return $this->espeak;
    }
}
