<?php

namespace Homie\Switches\Change;

use BrainExe\Annotations\Annotations\Service;
use Homie\Switches\SwitchInterface;
use Homie\Switches\VO\RadioVO;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Change.Gpio", public=false)
 */
class Gpio implements SwitchInterface
{

    /**
     * @param SwitchVO|RadioVO $switch
     * @param int $status
     */
    public function setStatus(SwitchVO $switch, int $status)
    {
        // TODO implement GPIO switch
    }
}
