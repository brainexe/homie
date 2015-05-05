<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Client\ClientInterface;
use Homie\Radio\VO\RadioVO;

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
     * @var RadioGateway
     */
    private $gateway;

    /**
     * @Inject({"@HomieClient", "@RadioGateway", "%rc_switch.command%"})
     * @param ClientInterface $client
     * @param RadioGateway $gateway
     * @param $rcSwitchCommand
     */
    public function __construct(ClientInterface $client, RadioGateway $gateway, $rcSwitchCommand)
    {
        $this->client          = $client;
        $this->rcSwitchCommand = $rcSwitchCommand;
        $this->gateway         = $gateway;
    }

    /**
     * @param RadioVO $radioVO
     * @param boolean $status
     */
    public function setStatus(RadioVO $radioVO, $status)
    {
        $this->gateway->editRadio($radioVO);

        $command = sprintf(
            '%s %s %d %d',
            $this->rcSwitchCommand,
            $radioVO->code,
            $radioVO->pin,
            (int)$status
        );
        $radioVO->status = (bool)$status;

        $this->client->execute($command);

    }
}
