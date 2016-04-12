<?php

namespace Homie\Espeak;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Client\ClientInterface;

/**
 * @Service("Espeak", public=false)
 */
class Espeak
{

    const DEFAULT_SPEAKER = 'de+m1';
    const DEFAULT_SPEED = 75;
    const DEFAULT_VOLUME = 120;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $command;

    /**
     * @Inject({
     *     "@HomieClient",
     *     "%espeak.command%"
     * })
     * @param ClientInterface $client
     * @param string $command
     */
    public function __construct(ClientInterface $client, string $command)
    {
        $this->client  = $client;
        $this->command = $command;
    }

    /**
     * @todo cache espeak --voices
     * @return array
     */
    public function getSpeakers() : array
    {
        return [
            'de+m1' => 'DE Male',
            'de+f1' => 'DE Female',
            'en'    => 'EN',
            'fr'    => 'FR'
        ];
    }

    /**
     * @param string $text
     * @param int $volume
     * @param int $speed
     * @param string $speaker
     */
    public function speak(
        string $text,
        int $volume,
        int $speed,
        string $speaker
    ) {
        if (empty($text)) {
            return;
        }

        $this->client->execute($this->command, [
            $text,
            '-s', $speed,
            '-a', $volume,
            '-v', $speaker
        ]);
    }
}
