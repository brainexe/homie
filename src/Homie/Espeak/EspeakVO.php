<?php

namespace Homie\Espeak;

class EspeakVO
{
    const DEVICE_SPEAKER = 1;
    const DEVICE_BROWSER = 2;

    const BROWSER_ONLY = self::DEVICE_BROWSER;
    const ALL_DEVICES  = self::DEVICE_SPEAKER | self::DEVICE_BROWSER;

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
     * @var int
     */
    public $devices;

    /**
     * @param string $text
     * @param int $volume
     * @param int $speed
     * @param string $speaker
     * @param int $devices
     */
    public function __construct(
        string $text,
        int $volume = null,
        int $speed = null,
        string $speaker = null,
        int $devices = self::ALL_DEVICES
    ) {
        $this->text    = $text;
        $this->volume  = $volume  ?: Espeak::DEFAULT_VOLUME;
        $this->speed   = $speed   ?: Espeak::DEFAULT_SPEED;
        $this->speaker = $speaker ?: Espeak::DEFAULT_SPEAKER;
        $this->devices = $devices;
    }
}
