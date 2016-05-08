<?php

namespace Homie\Espeak;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Client\ClientInterface;
use Iterator;

/**
 * @Service("Espeak.Speakers", public=false)
 */
class Speakers
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
     * @var string[]
     */
    private $locales;

    /**
     * @Inject({
     *     "@HomieClient",
     *     "%espeak.command%",
     *     "%locales%"
     * })
     * @param ClientInterface $client
     * @param string $command
     * @param string[] $locales
     */
    public function __construct(ClientInterface $client, string $command, array $locales)
    {
        $this->client  = $client;
        $this->command = $command;
        $this->locales = $locales;
    }

    /**
     * @return Iterator
     */
    public function getSpeakers() : Iterator
    {
        $raw = $this->getRawSpeakers();

        foreach ($raw as $idx => $line) {
            @list(,, $language, $gender, $voiceName) = preg_split('/\s+/', $line);

            if (strlen($language) <= 5 && in_array(substr($language, 0, 2), $this->locales)) {
                yield $language => ucfirst($voiceName) . " - $gender";
            }
        }
    }

    /**
     * @return string[]
     */
    private function getRawSpeakers() : array
    {
        try {
            $raw = $this->client->executeWithReturn($this->command, ['--voices']);

            return explode("\n", $raw);
        } catch (\RuntimeException $e) {
            return [];
        }
    }
}
