<?php

namespace Homie\Espeak;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;

class EspeakEvent extends AbstractEvent implements PushViaWebsocket
{

    const SPEAK = 'espeak.speak';

    /**
     * @var EspeakVO
     */
    private $espeak;

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
