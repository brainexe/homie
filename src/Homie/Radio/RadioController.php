<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Client\ClientInterface;
use Homie\Radio\VO\RadioVO;
use Homie\Radio\VO\SwitchVO;

/**
 * @Service("RadioController", public=false)
 */
class RadioController implements SwitchInterface
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
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject({
     *     "@HomieClient",
     *     "@RadioGateway",
     *     "%rc_switch.command%"
     * })
     * @param ClientInterface $client
     * @param Gateway $gateway
     * @param $rcSwitchCommand
     */
    public function __construct(
        ClientInterface $client,
        Gateway $gateway,
        $rcSwitchCommand
    ) {
        $this->client          = $client;
        $this->rcSwitchCommand = $rcSwitchCommand;
        $this->gateway         = $gateway;
    }

    /**
     * @param SwitchVO|RadioVO $switch
     * @param boolean $status
     */
    public function setStatus(SwitchVO $switch, $status)
    {
        $this->gateway->edit($switch);

        $command = sprintf(
            '%s %s %d %d',
            $this->rcSwitchCommand,
            escapeshellarg($switch->code),
            (int)$switch->pin,
            (int)$status
        );
        $switch->status = (bool)$status;

        $this->client->execute($command);
    }
}
