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

    const COMMAND = 'mplayer %s';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject("@HomieClient")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $file
     */
    public function playSound($file)
    {
        $command = sprintf(self::COMMAND, escapeshellarg($file));
        $this->client->execute($command);
    }
}
