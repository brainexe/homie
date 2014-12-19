<?php

namespace Raspberry\Espeak;

interface SpeakOutputInterface
{

    /**
     * @return string[]
     */
    public function getSpeakers();

    /**
     * @param string $text
     * @param integer $volume
     * @param integer $speed
     * @param string $speaker
     */
    public function speak($text, $volume = 100, $speed = 100, $speaker = Espeak::DEFAULT_SPEAKER);
}
