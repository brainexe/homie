<?php

namespace Homie\Espeak;

class EspeakVO
{

    /**
     * @var string
     */
    public $text;

    /**
     * @var integer
     */
    public $volume;

    /**
     * @var integer
     */
    public $speed;

    /**
     * @var string
     */
    public $speaker;

    /**
     * @param string $text
     * @param integer $volume
     * @param integer $speed
     * @param string $speaker
     */
    public function __construct(
        $text,
        $volume = null,
        $speed = null,
        $speaker = null
    ) {
        $this->text    = $text;
        $this->volume  = $volume ?: Espeak::DEFAULT_VOLUME;
        $this->speed   = $speed ?: Espeak::DEFAULT_SPEED;
        $this->speaker = $speaker ?: Espeak::DEFAULT_SPEAKER;
    }
}
