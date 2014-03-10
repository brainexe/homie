<?php

namespace Raspberry\Espeak;
use Raspberry\Client\ClientInterface;

/**
 * @Service
 */
class Espeak implements SpeakOutputInterface {

	/**
	 * @var ClientInterface
	 */
	private $_raspberry_client;

	/**
	 * @Inject("@RaspberryClient")
	 */
	public function __construct(ClientInterface $client) {
		$this->_raspberry_client = $client;
	}

	/**
	 * @return array
	 */
	public function getSpeakers() {
		return ['de+m1' => 'DE Male', 'de+f1' => 'DE Female', 'en' => 'EN', 'fr' => 'FR'];
	}

	/**
	 * @param string $text
	 * @param integer $volume
	 * @param integer $speed
	 * @param string $speaker
	 */
	public function speak($text, $volume = 100, $speed = 100, $speaker = 'de+m1') {
		if (empty($text)) {
			return;
		}

		$command = sprintf('espeak "%s" -s %d -a %d  -v%ss --stdout | aplay', $text, $speed, $volume, $speaker);

		$this->_raspberry_client->execute($command);
	}
} 