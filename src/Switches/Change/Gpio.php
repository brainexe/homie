<?php

namespace Homie\Switches\Change;


use BrainExe\Core\Annotations\Service;
use Homie\Gpio\GpioManager;

use Homie\Node\Gateway;
use Homie\Switches\SwitchInterface;
use Homie\Switches\VO\GpioSwitchVO;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Change.Gpio")
 */
class Gpio implements SwitchInterface
{
    /**
     * @var GpioManager
     */
    private $manager;

    /**
     * @var Gateway
     */
    private $nodes;

    /**
     * @param GpioManager $manager
     * @param Gateway $nodes
     */
    public function __construct(GpioManager $manager, Gateway $nodes)
    {
        $this->manager = $manager;
        $this->nodes   = $nodes;
    }

    /**
     * @param SwitchVO|GpioSwitchVO $switch
     * @param int $status
     */
    public function setStatus(SwitchVO $switch, int $status)
    {
        $node = $this->nodes->get($switch->nodeId);

        $this->manager->setPin($node, $switch->pin, true, (bool)$status);
    }
}
