<?php

namespace Homie\Switches\Change;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Client\ClientInterface;
use Homie\Switches\SwitchInterface;
use Homie\Switches\VO\RadioVO;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Change.Radio")
 */
class Radio implements SwitchInterface
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
     * @Inject({
     *     "@HomieClient",
     *     "%rc_switch.command%"
     * })
     * @param ClientInterface $client
     * @param $rcSwitchCommand
     */
    public function __construct(
        ClientInterface $client,
        string $rcSwitchCommand
    ) {
        $this->client          = $client;
        $this->rcSwitchCommand = $rcSwitchCommand;
    }

    /**
     * @param SwitchVO|RadioVO $switch
     * @param int $status
     */
    public function setStatus(SwitchVO $switch, int $status)
    {
        $this->client->execute($this->rcSwitchCommand, [
            $switch->code,
            (int)$switch->pin,
            $status
        ]);
    }
}
