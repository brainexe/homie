<?php

namespace Homie\Media;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class Sound
{

    const ROOT = ROOT . '/assets/sounds/';

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
     *     "%sound.executable%"
     * })
     * @param ClientInterface $client
     * @param string $command
     */
    public function __construct(ClientInterface $client, $command)
    {
        $this->client  = $client;
        $this->command = $command;
    }

    /**
     * @param string $file
     */
    public function playSound($file)
    {
        $this->client->execute($this->command, [self::ROOT . $file]);
    }
}
