<?php

namespace Homie\Espeak;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;
use Homie\Client\ClientInterface;
use Iterator;
use RuntimeException;

/**
 * @Service
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

        $shortLocales = array_map(function (string $locale) {
            return substr($locale, 0, 2);
        }, $this->locales);

        foreach ($raw as $idx => $line) {
            @list(,, $language, $gender, $voiceName) = preg_split('/\s+/', $line);

            if (strlen($language) <= 5 && in_array($language, $shortLocales)) {
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
        } catch (RuntimeException $e) {
            return [];
        }
    }
}
