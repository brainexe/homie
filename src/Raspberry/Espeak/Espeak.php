<?php

namespace Raspberry\Espeak;

class Espeak {
	public static function getSpeakers() {
		return [
			'de+m1' => 'DE Male',
			'de+f1' => 'DE Female',
			'en' => 'EN',
			'fr' => 'FR',
		];
	}

	/**
	 * @param string $text
	 * @param integer $volume
	 * @param integer $speed
	 * @param string $speaker
	 */
	public function speak($text, $volume = 100, $speed = 100, $speaker = 'de') {
		if (empty($text)) {
			return;
		}

		system(sprintf('espeak "%s" -s %d -a %d  -v%s --stdout | aplay', $text, $speed, $volume, $speaker));
	}
} 