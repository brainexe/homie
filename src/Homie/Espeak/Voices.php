<?php

namespace Homie\Espeak;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Generator;
use Homie\Client\ClientInterface;

/**
 * @todo
 * @Service("Espeak.Voices", public=false)
 */
class Voices
{
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
     * @return Generator
     */
    public function getSpeakers() : Generator
    {
        $raw = $this->client->executeWithReturn($this->command, ['--voices']);
        foreach (explode("\n", $raw) as $line) {
            list(, $language, $gender, $voiceName) = preg_split('/\s+/', $line);

            yield $language => "$voiceName - $gender";
        }
    }
}
