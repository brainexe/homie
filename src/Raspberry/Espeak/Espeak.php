<?php

namespace Raspberry\Espeak;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
class Espeak {
	/**
	 * @return array
	 */
	public function getSpeakers() {
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

		system(sprintf('espeak "%s" -s %d -a %d  -v%ss --stdout | aplay', $text, $speed, $volume, $speaker));
//		system(sprintf('tts -l %s %s', 'de', $text));
	}
} 