<?php

namespace Homie\Espeak;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\TimeTrait;

use Homie\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class Espeak
{

    use TimeTrait;

    const COMMAND = 'espeak'; // todo config

    const DEFAULT_SPEAKER = 'de+m1';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject({"@HomieClient"})
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getSpeakers()
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
     * @param integer $volume
     * @param integer $speed
     * @param string $speaker
     */
    public function speak(
        $text,
        $volume,
        $speed,
        $speaker
    ) {
        if (empty($text)) {
            return;
        }

        $this->client->execute(self::COMMAND, [
            $text,
            '-s', $speed,
            '-a', $volume,
            sprintf('-v%ss', $speaker)
        ]);
    }
}
