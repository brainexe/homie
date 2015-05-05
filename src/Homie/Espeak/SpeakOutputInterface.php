<?php

namespace Homie\Espeak;

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
    public function speak(
        $text,
        $volume,
        $speed,
        $speaker
    );
}
