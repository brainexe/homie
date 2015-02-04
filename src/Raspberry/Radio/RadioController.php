<?php

namespace Raspberry\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Raspberry\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class RadioController
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $rcSwitchCommand;

    /**
     * @Inject({"@RaspberryClient", "%rc_switch.command%"})
     * @param ClientInterface $client
     * @param $rcSwitchCommand
     */
    public function __construct(ClientInterface $client, $rcSwitchCommand)
    {
        $this->client = $client;
        $this->rcSwitchCommand = $rcSwitchCommand;
    }

    /**
     * @param string  $code
     * @param integer $number
     * @param boolean $status
     */
    public function setStatus($code, $number, $status)
    {
        $command = sprintf('%s %s %d %d', $this->rcSwitchCommand, $code, $number, (int)$status);

        $this->client->execute($command);
    }
}
