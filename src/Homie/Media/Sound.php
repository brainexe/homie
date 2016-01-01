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

    // todo configurable
    const COMMAND = 'mplayer';

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
        $this->client->execute(self::COMMAND, [self::ROOT . $file]);
    }
}
