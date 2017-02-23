<?php

namespace Homie\Media;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;
use Homie\Client\ClientInterface;

/**
 * @Service
 */
class Sound
{

    const DIRECTORY = ROOT . '/assets/sounds/';

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
     *     "%sound.command%"
     * })
     * @param ClientInterface $client
     * @param string $command
     */
    public function __construct(
        ClientInterface $client,
        string $command
    ) {
        $this->client  = $client;
        $this->command = $command;
    }

    /**
     * @param string $file
     */
    public function playSound(string $file) : void
    {
        $this->client->execute($this->command, [self::DIRECTORY . $file]);
    }
}
