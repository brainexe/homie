<?php

namespace Homie\Switches\Change;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Gpio\GpioManager;
use Homie\Switches\SwitchInterface;
use Homie\Switches\VO\GpioSwitchVO;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Change.Gpio", public=false)
 */
class Gpio implements SwitchInterface
{
    /**
     * @var GpioManager
     */
    private $manager;

    /**
     * @Inject({"@GPIO.GpioManager"})
     * @param GpioManager $manager
     */
    public function __construct(GpioManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param SwitchVO|GpioSwitchVO $switch
     * @param int $status
     */
    public function setStatus(SwitchVO $switch, int $status)
    {
        $this->manager->setPin($switch->pin, true, (bool)$status);
    }
}
