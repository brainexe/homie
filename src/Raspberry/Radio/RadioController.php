<?php

namespace Raspberry\Radio;

use Raspberry\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class RadioController
{
    const BASE_COMMAND = 'sudo /opt/rcswitch-pi/send';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject("@RaspberryClient")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $code
     * @param integer $number
     * @param boolean $status
     */
    public function setStatus($code, $number, $status)
    {
        $command = sprintf('%s %s %d %d', self::BASE_COMMAND, $code, $number, (int)$status);
        $this->client->execute($command);
    }
}
